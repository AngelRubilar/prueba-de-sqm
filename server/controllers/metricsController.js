const { getMetrics, getMetricsJSON } = require('../utils/metrics');
const logger = require('../config/logger');

/**
 * Controlador de métricas Prometheus
 */

/**
 * Endpoint de métricas en formato Prometheus
 * GET /metrics
 */
async function prometheusMetrics(req, res) {
  try {
    res.set('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
    const metrics = await getMetrics();
    res.send(metrics);
  } catch (error) {
    logger.error('Error obteniendo métricas:', error);
    res.status(500).send('Error obteniendo métricas');
  }
}

/**
 * Endpoint de métricas en formato JSON
 * GET /metrics/json
 */
async function metricsJSON(req, res) {
  try {
    const metrics = await getMetricsJSON();
    res.json({
      success: true,
      timestamp: new Date().toISOString(),
      metrics
    });
  } catch (error) {
    logger.error('Error obteniendo métricas JSON:', error);
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
}

module.exports = {
  prometheusMetrics,
  metricsJSON
};
