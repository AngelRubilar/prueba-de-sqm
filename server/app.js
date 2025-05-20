require('dotenv').config();
const moment = require('moment-timezone');
const express = require('express');
const cors = require('cors');
const path = require('path');

// Controllers
const serpramController = require('./controllers/serpramController');
const esinfaController = require('./controllers/esinfaController');
const aytController = require('./controllers/aytController');
const sercoambController = require('./controllers/sercoambController');
const reportController = require('./controllers/reportController');
const { cargarTimestampSerpram, guardarTimestampSerpram } = require('./store');
const logAnalyzer = require('./utils/logAnalyzer');

// Rutas Api Rest
const apiRoutes = require('./routes/apiRoutes');

// Iniciamos el servidor express
const app = express();

// Debug: mostrar FRONTEND_URL para verificar configuración
console.log('>>> FRONTEND_URL =', process.env.FRONTEND_URL);

// Configurar CORS para reflejar siempre el origen de la petición
app.use(cors({
  origin: true,
  credentials: true
}));

app.use(express.json());
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

    await Promise.all([
      ejecutarSerpram(),
      ejecutarAyt(),
      ejecutarSercoamb()
    ]);

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
/* // Servir build de React en producción (misma origen)
if (process.env.NODE_ENV === 'production') {
  app.use(express.static(path.join(__dirname, '../client/build')));
  app.get('*', (req, res) => {
    res.sendFile(path.join(__dirname, '../client/build', 'index.html'));
  });
}
 */

// Configuración para servir archivos estáticos
if (process.env.NODE_ENV === 'production') {
  // En producción, servir los archivos estáticos de React
  app.use(express.static(path.join(__dirname, '../client/build')));
  app.get('*', (req, res) => {
    res.sendFile(path.join(__dirname, '../client/build', 'index.html'));
  });
} else {
  // En desarrollo, solo servir la API
  app.use('/api', apiRoutes);
}

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Servidor iniciado en puerto ${PORT}`);
  console.log('Hora Chile:', formatearFechaChile());
  
  // Ejecutar inmediatamente las consultas iniciales
  ejecutarEsinfa();
  ejecutarTodasLasConsultas();
  
  // Configurar intervalos de consulta
  setInterval(ejecutarEsinfa, 300000); // 5 minutos
  setInterval(ejecutarTodasLasConsultas, 60000); // 1 minuto
  
  // Iniciar programación de reportes diarios
  programarReporteDiario();
});