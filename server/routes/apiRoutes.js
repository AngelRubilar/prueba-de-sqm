const express = require('express');
const router = express.Router();
const ctl = require('../controllers/measurementController');
const reportController = require('../controllers/reportController');

// Endpoint para PM10
router.get('/datos-PM10', ctl.getPM10Data);
// Endpoint para viento
router.get('/datos-viento', ctl.getWindData);
// Endpoint para SO2
router.get('/datos-SO2', ctl.getSO2Data);
// Endpoint para múltiples variables
router.get('/datos-variables', ctl.getVariablesData);

// Endpoint para generación de reporte
router.get('/reportes/logs', reportController.generateReport);

// Endpoint para prueba de envío de correo
router.get('/reportes/test-email', reportController.testEmailConfig);

module.exports = router;