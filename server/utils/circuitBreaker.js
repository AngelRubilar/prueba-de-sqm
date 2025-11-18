const CircuitBreaker = require('opossum');
const logger = require('../config/logger');

/**
 * Configuración por defecto para Circuit Breakers
 */
const defaultOptions = {
  timeout: 10000, // 10 segundos
  errorThresholdPercentage: 50, // Abre el circuito si 50% de requests fallan
  resetTimeout: 30000, // 30 segundos antes de intentar cerrar el circuito
  rollingCountTimeout: 10000, // Ventana de 10 segundos para contar errores
  rollingCountBuckets: 10, // Dividir la ventana en 10 buckets
  name: 'GenericCircuitBreaker'
};

/**
 * Crear un Circuit Breaker para una función asíncrona
 *
 * @param {Function} action - Función asíncrona a proteger
 * @param {Object} options - Opciones de configuración
 * @returns {CircuitBreaker} Instancia del circuit breaker
 */
function createCircuitBreaker(action, options = {}) {
  const config = { ...defaultOptions, ...options };
  const breaker = new CircuitBreaker(action, config);

  // Eventos del Circuit Breaker
  breaker.on('open', () => {
    logger.warn(`Circuit Breaker [${config.name}] ABIERTO - Demasiados errores detectados`);
  });

  breaker.on('halfOpen', () => {
    logger.info(`Circuit Breaker [${config.name}] SEMI-ABIERTO - Probando si el servicio se recuperó`);
  });

  breaker.on('close', () => {
    logger.info(`Circuit Breaker [${config.name}] CERRADO - Servicio funcionando normalmente`);
  });

  breaker.on('timeout', () => {
    logger.warn(`Circuit Breaker [${config.name}] - Timeout excedido`);
  });

  breaker.on('reject', () => {
    logger.warn(`Circuit Breaker [${config.name}] - Request rechazada (circuito abierto)`);
  });

  breaker.on('success', (result) => {
    logger.debug(`Circuit Breaker [${config.name}] - Request exitosa`);
  });

  breaker.on('failure', (error) => {
    logger.error(`Circuit Breaker [${config.name}] - Request falló:`, {
      error: error.message,
      stack: error.stack
    });
  });

  return breaker;
}

/**
 * Crear Circuit Breaker para API externa con configuración específica
 *
 * @param {string} apiName - Nombre de la API
 * @param {Function} apiFunction - Función que realiza la llamada a la API
 * @param {Object} customOptions - Opciones personalizadas
 * @returns {CircuitBreaker} Instancia del circuit breaker
 */
function createApiCircuitBreaker(apiName, apiFunction, customOptions = {}) {
  const options = {
    timeout: 15000, // APIs externas pueden tardar más
    errorThresholdPercentage: 50,
    resetTimeout: 60000, // 1 minuto para APIs externas
    name: `API-${apiName}`,
    ...customOptions
  };

  const breaker = createCircuitBreaker(apiFunction, options);

  // Fallback para APIs externas
  breaker.fallback(() => {
    logger.warn(`Circuit Breaker [${options.name}] - Usando fallback`);
    return {
      success: false,
      error: 'Service temporarily unavailable',
      fallback: true
    };
  });

  return breaker;
}

/**
 * Obtener estadísticas del Circuit Breaker
 *
 * @param {CircuitBreaker} breaker - Instancia del circuit breaker
 * @returns {Object} Estadísticas del circuit breaker
 */
function getBreakerStats(breaker) {
  const stats = breaker.stats;

  return {
    name: breaker.name,
    state: breaker.opened ? 'OPEN' : (breaker.halfOpen ? 'HALF_OPEN' : 'CLOSED'),
    fires: stats.fires,
    successes: stats.successes,
    failures: stats.failures,
    rejects: stats.rejects,
    timeouts: stats.timeouts,
    fallbacks: stats.fallbacks,
    latencyMean: stats.latencyMean,
    percentiles: {
      p50: stats.percentiles['0.5'],
      p90: stats.percentiles['0.9'],
      p95: stats.percentiles['0.95'],
      p99: stats.percentiles['0.99']
    }
  };
}

/**
 * Health check para Circuit Breakers
 *
 * @param {Array<CircuitBreaker>} breakers - Array de circuit breakers a verificar
 * @returns {Object} Estado de salud de los circuit breakers
 */
function healthCheck(breakers) {
  const breakersHealth = breakers.map(breaker => {
    const stats = getBreakerStats(breaker);
    const isHealthy = !breaker.opened;

    return {
      name: breaker.name,
      healthy: isHealthy,
      state: stats.state,
      stats: {
        successRate: stats.fires > 0
          ? ((stats.successes / stats.fires) * 100).toFixed(2) + '%'
          : 'N/A',
        totalRequests: stats.fires,
        failures: stats.failures,
        timeouts: stats.timeouts
      }
    };
  });

  const allHealthy = breakersHealth.every(b => b.healthy);

  return {
    status: allHealthy ? 'healthy' : 'degraded',
    breakers: breakersHealth,
    timestamp: new Date().toISOString()
  };
}

module.exports = {
  createCircuitBreaker,
  createApiCircuitBreaker,
  getBreakerStats,
  healthCheck
};
