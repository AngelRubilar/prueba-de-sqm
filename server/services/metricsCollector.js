const {
  updateCircuitBreakerMetrics,
  updateQueueMetrics,
  updateDatabaseMetrics
} = require('../utils/metrics');
const queueManager = require('../utils/queueManager');
const logger = require('../config/logger');

/**
 * Servicio de colección de métricas
 *
 * Actualiza métricas periódicamente desde diferentes fuentes
 */

class MetricsCollector {
  constructor() {
    this.intervalId = null;
    this.collectionInterval = 15000; // 15 segundos
  }

  /**
   * Iniciar colección periódica de métricas
   */
  start() {
    if (this.intervalId) {
      logger.warn('MetricsCollector ya está en ejecución');
      return;
    }

    logger.info('Iniciando MetricsCollector');

    // Colectar métricas inmediatamente
    this.collect();

    // Programar colección periódica
    this.intervalId = setInterval(() => {
      this.collect();
    }, this.collectionInterval);
  }

  /**
   * Detener colección de métricas
   */
  stop() {
    if (this.intervalId) {
      clearInterval(this.intervalId);
      this.intervalId = null;
      logger.info('MetricsCollector detenido');
    }
  }

  /**
   * Colectar todas las métricas
   */
  async collect() {
    try {
      await Promise.all([
        this.collectCircuitBreakerMetrics(),
        this.collectQueueMetrics(),
        this.collectDatabaseMetrics()
      ]);
    } catch (error) {
      logger.error('Error colectando métricas:', error);
    }
  }

  /**
   * Colectar métricas de Circuit Breakers
   */
  async collectCircuitBreakerMetrics() {
    try {
      // Lazy load para evitar dependencias circulares
      const serpramService = require('./serpramService');
      const aytService = require('./aytService');
      const esinfaService = require('./esinfaService');
      const sercoambService = require('./sercoambService');

      // SERPRAM
      if (typeof serpramService.getCircuitBreakerStats === 'function') {
        const stats = serpramService.getCircuitBreakerStats();
        updateCircuitBreakerMetrics('SERPRAM', stats);
      }

      // AYT
      if (typeof aytService.getCircuitBreakerStats === 'function') {
        const stats = aytService.getCircuitBreakerStats();
        updateCircuitBreakerMetrics('AYT', stats);
      }

      // ESINFA
      if (typeof esinfaService.getCircuitBreakerStats === 'function') {
        const stats = esinfaService.getCircuitBreakerStats();
        updateCircuitBreakerMetrics('ESINFA', stats);
      }

      // SERCOAMB
      if (typeof sercoambService.getCircuitBreakerStats === 'function') {
        const stats = sercoambService.getCircuitBreakerStats();

        if (stats.tamentica) {
          updateCircuitBreakerMetrics('SERCOAMB-Tamentica', stats.tamentica);
        }
        if (stats.victoria) {
          updateCircuitBreakerMetrics('SERCOAMB-Victoria', stats.victoria);
        }
      }
    } catch (error) {
      logger.debug('Error colectando métricas de Circuit Breakers:', error.message);
    }
  }

  /**
   * Colectar métricas de colas Bull
   */
  async collectQueueMetrics() {
    try {
      const stats = await queueManager.getAllQueuesStats();

      for (const [queueName, queueStats] of Object.entries(stats)) {
        updateQueueMetrics(queueName, queueStats);
      }
    } catch (error) {
      logger.debug('Error colectando métricas de colas:', error.message);
    }
  }

  /**
   * Colectar métricas de base de datos
   */
  async collectDatabaseMetrics() {
    try {
      // Lazy load para evitar dependencias circulares
      const { readerPool, writerPool } = require('../config/database');

      // Writer pool
      if (writerPool && writerPool.pool) {
        const connections = {
          total: writerPool.pool._allConnections?.length || 0,
          free: writerPool.pool._freeConnections?.length || 0,
          queued: writerPool.pool._connectionQueue?.length || 0
        };
        updateDatabaseMetrics('writer', connections);
      }

      // Reader pool
      if (readerPool && readerPool.pool) {
        const connections = {
          total: readerPool.pool._allConnections?.length || 0,
          free: readerPool.pool._freeConnections?.length || 0,
          queued: readerPool.pool._connectionQueue?.length || 0
        };
        updateDatabaseMetrics('reader', connections);
      }
    } catch (error) {
      logger.debug('Error colectando métricas de base de datos:', error.message);
    }
  }
}

// Exportar instancia singleton
const metricsCollector = new MetricsCollector();

module.exports = metricsCollector;
