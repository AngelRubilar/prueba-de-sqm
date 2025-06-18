require('dotenv').config();
const moment = require('moment-timezone');
const express = require('express');
const cors = require('cors');
const path = require('path');
const rateLimit = require('express-rate-limit');

// Controllers
const serpramController = require('./controllers/serpramController');
const esinfaController = require('./controllers/esinfaController');
const aytController = require('./controllers/aytController');
const sercoambController = require('./controllers/sercoambController');
const reportController = require('./controllers/reportController');
const { cargarTimestampSerpram, guardarTimestampSerpram } = require('./store');
const logAnalyzer = require('./utils/logAnalyzer');
const requestLogger = require('./middleware/requestLogger');

// Inicializar el servicio MQTT correctamente
const MqttService = require('./services/mqttService');
const mqttService = new MqttService();

// Rutas Api Rest
const apiRoutes = require('./routes/apiRoutes');

const forecastScheduler = require('./services/forecastScheduler');

// Iniciamos el servidor express
const app = express();

// Configurar Express para confiar en el proxy de manera específica
app.set('trust proxy', 'loopback');

// Configuración de rate limiting para desarrollo (más permisivo)
const limiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutos
  max: 500, // límite más alto para desarrollo
  message: 'Demasiadas peticiones desde esta IP, por favor intente más tarde',
  standardHeaders: true,
  legacyHeaders: false,
  trustProxy: true,
  skip: (req) => {
    // No aplicar rate limiting a las rutas de React
    return req.path.startsWith('/react/');
  }
});

// Middleware
app.use(requestLogger);
app.use(limiter);

// Debug: mostrar FRONTEND_URL para verificar configuración
console.log('>>> FRONTEND_URL =', process.env.FRONTEND_URL);

// // Configurar CORS para desarrollo
// app.use(cors({
//   origin: true, // Más permisivo en desarrollo
//   credentials: true,
//   methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
//   allowedHeaders: ['Content-Type', 'Authorization']
// }));

// Middleware de seguridad (mantenido pero con headers más permisivos para desarrollo)
app.use((req, res, next) => {
  res.setHeader('X-Content-Type-Options', 'nosniff');
  //res.setHeader('X-Frame-Options', 'SAMEORIGIN');
  res.setHeader('X-Frame-Options', 'ALLOW-FROM http://localhost:3000');
  res.setHeader('X-XSS-Protection', '1; mode=block');
  next();
});

app.use(express.json({ limit: '10mb' }));
app.use('/api', apiRoutes);

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

// Configuración para servir archivos estáticos en desarrollo
if (process.env.NODE_ENV === 'development') {
  console.log('Configurando rutas para desarrollo...');
  console.log('Ruta del build:', path.join(__dirname, '../client/build'));
  
  // Servir archivos estáticos de React bajo la ruta /react
  app.use('/react', express.static(path.join(__dirname, '../client/build')));
  
  // Manejar todas las rutas de React
  app.get('/react/*', (req, res) => {
    res.sendFile(path.join(__dirname, '../client/build', 'index.html'));
  });

  // Manejar la ruta raíz redirigiendo a /react
  app.get('/', (req, res) => {
    res.redirect('/react');
  });
}

// Manejador de errores global
app.use((err, req, res, next) => {
  console.error('Error global:', err);
  res.status(500).json({
    error: 'Error interno del servidor',
    message: err.message // En desarrollo mostramos el mensaje de error completo
  });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Servidor iniciado en puerto ${PORT}`);
  console.log('Hora Chile:', formatearFechaChile());
  console.log('Ambiente: Desarrollo');
  
   
  // Ejecutar inmediatamente las consultas iniciales
  ejecutarEsinfa();
  ejecutarTodasLasConsultas();
  
  // Configurar intervalos de consulta
  setInterval(ejecutarEsinfa, 300000); // 5 minutos
  setInterval(ejecutarTodasLasConsultas, 60000); // 1 minuto
  
  // Iniciar programación de reportes diarios
  programarReporteDiario();

  // Iniciar el programador de pronóstico
  forecastScheduler.start();
});

// Manejo de señales de terminación
process.on('SIGTERM', () => {
  console.log('Recibida señal SIGTERM. Cerrando servidor...');
  forecastScheduler.stop();
  process.exit(0);
});

process.on('SIGINT', () => {
  console.log('Recibida señal SIGINT. Cerrando servidor...');
  forecastScheduler.stop();
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