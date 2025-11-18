const express = require('express');
const router = express.Router();
const metricsController = require('../controllers/metricsController');

/**
 * Rutas de Métricas Prometheus
 *
 * /metrics - Formato Prometheus para scraping
 * /metrics/json - Formato JSON para debugging
 */

// Endpoint para Prometheus (formato texto)
router.get('/', metricsController.prometheusMetrics);

// Endpoint para visualización JSON
router.get('/json', metricsController.metricsJSON);

module.exports = router;
