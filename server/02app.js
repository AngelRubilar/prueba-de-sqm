require('dotenv').config();

// Configurar entorno de producción
process.env.NODE_ENV = process.env.NODE_ENV || 'production';

const moment = require('moment-timezone');
const express = require('express');
const path = require('path');
const fs = require('fs');
const rateLimit = require('express-rate-limit');
const { iniciarProcesoProgramado } = require('./traspaso');

// Controllers
const serpramController = require('./controllers/serpramController');
const esinfaController = require('./controllers/esinfaController');
const aytController = require('./controllers/aytController');
const sercoambController = require('./controllers/sercoambController');
const reportController = require('./controllers/reportController');
const { cargarTimestampSerpram, guardarTimestampSerpram } = require('./store');
const logAnalyzer = require('./utils/logAnalyzer');
const requestLogger = require('./middleware/requestLogger');
const MqttService = require('./services/mqttService');
const forecastScheduler = require('./services/forecastScheduler');

// Inicializar MQTT
const mqttService = new MqttService();

// Rutas Api Rest
const apiRoutes = require('./routes/apiRoutes');

const AverageScheduler = require('./services/averageScheduler');

// Iniciamos el servidor express
const app = express();

// Configurar Express para confiar en el proxy de manera específica
app.set('trust proxy', 'loopback');

// Configuración de rate limiting para producción (solo APIs)
const limiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutos
  max: 100, // límite de 100 peticiones por ventana
  message: 'Demasiadas peticiones desde esta IP, por favor intente más tarde',
  standardHeaders: true,
  legacyHeaders: false,
  trustProxy: true,
  skip: (req) => {
    // No aplicar rate limiting a las rutas de React ni archivos estáticos
    return !req.path.startsWith('/api') || req.path.startsWith('/react/');
  }
});

// Middleware de debugging para todas las rutas
app.use((req, res, next) => {
  console.log(`🔍 [DEBUG] ${req.method} ${req.path} - ${new Date().toISOString()}`);
  console.log(`🔍 [DEBUG] Headers:`, req.headers);
  console.log(`🔍 [DEBUG] User-Agent:`, req.get('User-Agent'));
  next();
});

// Middleware
app.use(requestLogger);
app.use(limiter);

// CORS no necesario en monolito de producción
// app.use(cors({...}));

// Middleware de seguridad
app.use((req, res, next) => {
  res.setHeader('X-Content-Type-Options', 'nosniff');
  res.setHeader('X-Frame-Options', 'SAMEORIGIN');
  res.setHeader('X-XSS-Protection', '1; mode=block');
  next();
});

app.use(express.json({ limit: '10mb' }));
app.use('/api', apiRoutes);

// Endpoint de prueba para verificar que el servidor funciona
app.get('/test-server', (req, res) => {
  console.log('✅ Endpoint de prueba accedido');
  res.json({ 
    message: 'Servidor funcionando correctamente',
    timestamp: new Date().toISOString(),
    path: req.path,
    url: req.originalUrl
  });
});

// Configuración para servir archivos estáticos en producción
app.use(express.static(path.join(__dirname, '../client/build')));

// Manejar todas las rutas de React (SPA - Single Page Application)
app.get('*', (req, res) => {
  console.log(`🌐 SPA route: ${req.method} ${req.path} - ${new Date().toISOString()}`);
  console.log(`🌐 URL completa: ${req.protocol}://${req.get('host')}${req.originalUrl}`);
  
  const indexPath = path.join(__dirname, '../client/build', 'index.html');
  console.log(`📁 Sirviendo archivo: ${indexPath}`);
  console.log(`📁 Archivo existe: ${fs.existsSync(indexPath)}`);
  
  if (!fs.existsSync(indexPath)) {
    console.error(`❌ Archivo no encontrado: ${indexPath}`);
    return res.status(404).send('Archivo index.html no encontrado. Asegúrate de ejecutar npm run build en el cliente.');
  }
  
  res.sendFile(indexPath, (err) => {
    if (err) {
      console.error(`❌ Error enviando archivo: ${err.message}`);
      res.status(500).send('Error interno del servidor');
    } else {
      console.log(`✅ Archivo enviado exitosamente para: ${req.path}`);
    }
  });
});

// Función para ejecutar Serpram
async function ejecutarSerpram() {
  try {
    const since = cargarTimestampSerpram();
    console.log('Serpram – consultando desde:', since);

    const resultados = await serpramController.realizarConsulta({ since });
    if (!Array.isArray(resultados)) {
      console.warn('La consulta Serpram NO devolvió un array. Abortando manejo de timestamps.');
      return;
    }

    if (resultados.length > 0) {
      const ultimoRegistro = resultados[resultados.length - 1];
      const [nuevoTs] = ultimoRegistro;
      guardarTimestampSerpram(nuevoTs);
    }
  } catch (error) {
    console.error('Error en ejecución Serpram:', error.message);
  }
}

// Función para ejecutar Esinfa
async function ejecutarEsinfa() {
  try {
    console.log('Iniciando consulta Esinfa:', new Date().toISOString());
    await esinfaController.realizarConsulta();
  } catch (error) {
    console.error('Error en la ejecución de Esinfa:', error.message);
  }
}

// Función para ejecutar AYT
async function ejecutarAyt() {
  try {
    console.log('Iniciando consulta AYT:', new Date().toISOString());
    await aytController.obtenerDatosEstacionesProgramado();
  } catch (error) {
    console.error('Error en la ejecución de AYT:', error.message);
  }
}

// Función para ejecutar Sercoamb
async function ejecutarSercoamb() {
  try {
    console.log('Iniciando consulta Sercoamb:', new Date().toISOString());
    await sercoambController.realizarConsulta();
  } catch (error) {
    console.error('Error en la ejecución de Sercoamb:', error.message);
  }
}

function formatearFechaChile() {
  return moment().tz('America/Santiago').format('YYYY-MM-DD HH:mm:ss');
}

// Función para ejecutar todas las consultas
async function ejecutarTodasLasConsultas() {
  try {
    console.log('\n=== Iniciando ciclo de consultas ===');
    console.log('Hora Chile:', formatearFechaChile());

    // Ejecutar Serpram primero
    console.log('Iniciando consulta Serpram...');
    await ejecutarSerpram();
    console.log('Consulta Serpram completada');

    // Esperar 5 segundos antes de la siguiente consulta
    await new Promise(resolve => setTimeout(resolve, 5000));

    // Ejecutar AYT
    console.log('Iniciando consulta AYT...');
    await ejecutarAyt();
    console.log('Consulta AYT completada');

    // Esperar 5 segundos antes de la siguiente consulta
    await new Promise(resolve => setTimeout(resolve, 5000));

    // Ejecutar Sercoamb
    console.log('Iniciando consulta Sercoamb...');
    await ejecutarSercoamb();
    console.log('Consulta Sercoamb completada');

    console.log(`=== Ciclo de consultas completado (${formatearFechaChile()}) ===\n`);
  } catch (error) {
    console.error(`Error en el ciclo de consultas (${formatearFechaChile()}):`, error.message);
  }
}

// Función para enviar reporte diario
async function enviarReporteDiario() {
  try {
    console.log('Iniciando envío de reporte diario:', formatearFechaChile());
    await logAnalyzer.generateAndSendReport();
    console.log('Reporte diario enviado exitosamente');
  } catch (error) {
    console.error('Error al enviar reporte diario:', error);
  }
}

// Función para programar el envío de reportes diarios
function programarReporteDiario() {
    const ahora = moment().tz('America/Santiago');
    const horaObjetivo = moment().tz('America/Santiago').set({ hour: 1, minute: 0, second: 0 });
    
    // Si la hora actual es después de la hora objetivo, programar para mañana
    if (ahora.isAfter(horaObjetivo)) {
        horaObjetivo.add(1, 'day');
    }
    
    const tiempoHastaReporte = horaObjetivo.diff(ahora);
    
    console.log(`Próximo reporte programado para: ${horaObjetivo.format('YYYY-MM-DD HH:mm:ss')}`);
    
    // Usar solo setInterval para programar el reporte
    setInterval(enviarReporteDiario, 24 * 60 * 60 * 1000);
    
    // Si el tiempo hasta el reporte es menor a 24 horas, ejecutar inmediatamente
    if (tiempoHastaReporte < 24 * 60 * 60 * 1000) {
        setTimeout(enviarReporteDiario, tiempoHastaReporte);
    }
}

// Función para sincronización nocturna AYT
async function ejecutarSincronizacionNocturnaAyt() {
  try {
    console.log('🌙 Iniciando sincronización nocturna AYT programada');
    console.log('🌙 Hora actual:', formatearFechaChile());
    
    // Verificar que no haya otras consultas AYT ejecutándose
    console.log('🌙 Verificando que no haya conflictos con consultas AYT normales...');
    
    const aytService = require('./services/aytService');
    console.log('🌙 Servicio AYT cargado, ejecutando sincronización nocturna...');
    
    // Agregar un pequeño delay para evitar conflictos
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    const resultados = await aytService.sincronizacionNocturna();
    console.log('✅ Sincronización nocturna AYT completada:', resultados);
    console.log('🌙 Hora de finalización:', formatearFechaChile());
  } catch (error) {
    console.error('❌ Error en sincronización nocturna AYT:', error.message);
    console.error('❌ Stack trace:', error.stack);
  }
}

// Función para programar la sincronización nocturna AYT
function programarSincronizacionNocturnaAyt() {
    console.log('🌙 Iniciando programación de sincronización nocturna AYT...');
    
    const ahora = moment().tz('America/Santiago');
    const horaObjetivo = moment().tz('America/Santiago').set({ hour: 2, minute: 0, second: 0 });
    
    console.log(`🌙 Hora actual (Chile): ${ahora.format('YYYY-MM-DD HH:mm:ss')}`);
    console.log(`🌙 Hora objetivo inicial: ${horaObjetivo.format('YYYY-MM-DD HH:mm:ss')}`);
    
    // Si la hora actual es después de la hora objetivo, programar para mañana
    if (ahora.isAfter(horaObjetivo)) {
        horaObjetivo.add(1, 'day');
        console.log(`🌙 Hora objetivo ajustada para mañana: ${horaObjetivo.format('YYYY-MM-DD HH:mm:ss')}`);
    }
    
    const tiempoHastaSincronizacion = horaObjetivo.diff(ahora);
    const horasHastaSincronizacion = Math.floor(tiempoHastaSincronizacion / (1000 * 60 * 60));
    const minutosHastaSincronizacion = Math.floor((tiempoHastaSincronizacion % (1000 * 60 * 60)) / (1000 * 60));
    
    console.log(`🌙 Tiempo hasta sincronización: ${horasHastaSincronizacion}h ${minutosHastaSincronizacion}m`);
    console.log(`🌙 Próxima sincronización nocturna AYT programada para: ${horaObjetivo.format('YYYY-MM-DD HH:mm:ss')}`);
    
    // Programar para ejecutar todos los días a las 2:00 AM
    const intervaloDiario = 24 * 60 * 60 * 1000; // 24 horas en milisegundos
    setInterval(() => {
        console.log('🌙 Ejecutando sincronización nocturna AYT programada (intervalo diario)');
        ejecutarSincronizacionNocturnaAyt();
    }, intervaloDiario);
    
    console.log(`🌙 Intervalo diario configurado: ${intervaloDiario}ms (${intervaloDiario / (1000 * 60 * 60)} horas)`);
    
    // Si el tiempo hasta la sincronización es menor a 24 horas, ejecutar inmediatamente
    if (tiempoHastaSincronizacion < 24 * 60 * 60 * 1000) {
        console.log(`🌙 Programando ejecución inmediata en ${tiempoHastaSincronizacion}ms`);
        setTimeout(() => {
            console.log('🌙 Ejecutando sincronización nocturna AYT programada (ejecución inmediata)');
            ejecutarSincronizacionNocturnaAyt();
        }, tiempoHastaSincronizacion);
    } else {
        console.log('🌙 No se programa ejecución inmediata (más de 24 horas hasta la próxima sincronización)');
    }
    
    console.log('🌙 Programación de sincronización nocturna AYT completada');
}

// Manejador de errores global
app.use((err, req, res, next) => {
  console.error('Error global:', err);
  res.status(500).json({
    error: 'Error interno del servidor',
    message: process.env.NODE_ENV === 'production' ? 'Ha ocurrido un error' : err.message
  });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`🚀 Servidor iniciado en puerto ${PORT}`);
  console.log('🕐 Hora Chile:', formatearFechaChile());
  console.log('🏗️ Ambiente:', process.env.NODE_ENV);
  console.log('📁 Directorio actual:', __dirname);
  console.log('📁 Ruta del build:', path.join(__dirname, '../client/build'));
  
  // Ejecutar inmediatamente las consultas iniciales
  ejecutarEsinfa();
  ejecutarTodasLasConsultas();
  
  // Configurar intervalos de consulta
  setInterval(ejecutarEsinfa, 300000); // 5 minutos
  setInterval(ejecutarTodasLasConsultas, 60000); // 1 minuto
  
  // Iniciar programación de reportes diarios
  programarReporteDiario();

  // Iniciar programación de sincronización nocturna AYT
  programarSincronizacionNocturnaAyt();

  // Iniciar el proceso programado de traspaso de datos
  iniciarProcesoProgramado();

  // Iniciar el programador de pronóstico
  forecastScheduler.start();

  // Iniciar el scheduler de promedios
  const averageScheduler = new AverageScheduler();
  averageScheduler.start();
});

// Manejo de señales de terminación
process.on('SIGTERM', () => {
  console.log('Recibida señal SIGTERM. Cerrando servidor...');
  forecastScheduler.stop();
  if (averageScheduler) {
    averageScheduler.stop();
  }
  process.exit(0);
});

process.on('SIGINT', () => {
  console.log('Recibida señal SIGINT. Cerrando servidor...');
  forecastScheduler.stop();
  if (averageScheduler) {
    averageScheduler.stop();
  }
  process.exit(0);
});

// Manejo de errores no capturados
process.on('uncaughtException', (err) => {
  console.error('Error no capturado:', err);
  process.exit(1);
});

process.on('unhandledRejection', (reason, promise) => {
  console.error('Promesa rechazada no manejada:', reason);
  process.exit(1);
});