const express = require('express');
const router = express.Router();
const ctl = require('../controllers/measurementController');
const reportController = require('../controllers/reportController');
const forecastController = require('../controllers/forecastController');
const AverageController = require('../controllers/averageController');
const averageController = new AverageController();

const {
  graphDataLimiter,
  multipleVariablesLimiter,
  reportLimiter,
  emailTestLimiter,
  forecastLimiter
} = require('../middleware/rateLimiters');

// Endpoint para PM10 con rate limit específico
router.get('/datos-PM10', graphDataLimiter, ctl.getPM10Data);

// Endpoint para viento con rate limit específico
router.get('/datos-viento', graphDataLimiter, ctl.getWindData);

// Endpoint para viento específico de la estación Hospital con rate limit específico
router.get('/datos-viento-hospital', graphDataLimiter, ctl.getHospitalWindData);

// Endpoint para SO2 con rate limit específico
router.get('/datos-SO2', graphDataLimiter, ctl.getSO2Data);

// Endpoint para múltiples variables con rate limit específico
router.get('/datos-variables', multipleVariablesLimiter, ctl.getVariablesData);

// Endpoint para generación de reporte con rate limit específico
router.get('/reportes/logs', reportLimiter, reportController.generateReport);

// Endpoint para prueba de envío de correo con rate limit específico
router.get('/reportes/test-email', emailTestLimiter, reportController.testEmailConfig);

// Endpoint para datos de pronóstico con rate limit específico
router.get('/datos-pronostico-SO2', forecastLimiter, forecastController.getSO2Forecast);

// Endpoint para control del programador de pronóstico
router.get('/forecast/status', forecastController.getForecastStatus);
router.post('/forecast/force-update', forecastController.forceForecastUpdate);

// Endpoint de debug para pronósticos (sin rate limit para facilitar debugging)
router.get('/forecast/debug', forecastController.debugForecastData);

// ===== ENDPOINTS DE PROMEDIOS =====

// Obtener últimos promedios para todas las estaciones
router.get('/promedios', graphDataLimiter, averageController.obtenerUltimosPromedios.bind(averageController));

// Obtener promedios para una estación específica
router.get('/promedios/estacion/:stationName', graphDataLimiter, averageController.obtenerPromediosEstacion.bind(averageController));

// Obtener promedios históricos para una estación y variable
router.get('/promedios/historico/:stationName/:variableName', graphDataLimiter, averageController.obtenerPromediosHistoricos.bind(averageController));

// Obtener configuración de variables por estación
router.get('/promedios/configuracion', graphDataLimiter, averageController.obtenerConfiguracion.bind(averageController));

// Control del scheduler de promedios
router.get('/promedios/scheduler/status', averageController.obtenerEstadoScheduler.bind(averageController));
router.post('/promedios/scheduler/start', averageController.iniciarScheduler.bind(averageController));
router.post('/promedios/scheduler/stop', averageController.detenerScheduler.bind(averageController));

// Ejecutar cálculo manual de promedios
router.post('/promedios/calcular', averageController.ejecutarCalculoManual.bind(averageController));

module.exports = router;