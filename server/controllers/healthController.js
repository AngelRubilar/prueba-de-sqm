const { healthCheckService, HealthCheckService } = require('../utils/healthCheck');
const { readerPool, writerPool } = require('../config/database');
const stateStore = require('../services/stateStore');
const serpramService = require('../services/serpramService');
const aytService = require('../services/aytService');
const esinfaService = require('../services/esinfaService');
const sercoambService = require('../services/sercoambService');
const logger = require('../config/logger');

/**
 * Controlador de Health Checks
 */

/**
 * Health check básico - Solo verifica que el servidor está vivo
 * GET /health
 */
async function basicHealth(req, res) {
  try {
    const health = await HealthCheckService.checkBasic();
    res.status(200).json({
      status: 'healthy',
      timestamp: new Date().toISOString(),
      uptime: healthCheckService.getUptime(),
      ...health.details
    });
  } catch (error) {
    logger.error('Error en basic health check:', error);
    res.status(503).json({
      status: 'unhealthy',
      error: error.message
    });
  }
}

/**
 * Health check completo - Verifica todos los componentes
 * GET /health/detailed
 */
async function detailedHealth(req, res) {
  try {
    // Registrar todos los servicios si no están registrados
    if (healthCheckService.services.size === 0) {
      await registerAllServices();
    }

    const health = await healthCheckService.checkAll();

    const statusCode = health.status === 'healthy' ? 200 :
                      health.status === 'degraded' ? 200 : 503;

    res.status(statusCode).json(health);
  } catch (error) {
    logger.error('Error en detailed health check:', error);
    res.status(503).json({
      status: 'unhealthy',
      error: error.message,
      timestamp: new Date().toISOString()
    });
  }
}

/**
 * Health check de base de datos
 * GET /health/database
 */
async function databaseHealth(req, res) {
  try {
    const writerHealth = await HealthCheckService.checkMySQLWriter(writerPool);
    const readerHealth = await HealthCheckService.checkMySQLReader(readerPool);

    const allHealthy = writerHealth.status === 'healthy' && readerHealth.status === 'healthy';

    res.status(allHealthy ? 200 : 503).json({
      status: allHealthy ? 'healthy' : 'unhealthy',
      timestamp: new Date().toISOString(),
      writer: writerHealth,
      reader: readerHealth
    });
  } catch (error) {
    logger.error('Error en database health check:', error);
    res.status(503).json({
      status: 'unhealthy',
      error: error.message
    });
  }
}

/**
 * Health check de Redis
 * GET /health/redis
 */
async function redisHealth(req, res) {
  try {
    const health = await HealthCheckService.checkRedis(stateStore);

    res.status(health.status === 'healthy' ? 200 : 503).json({
      ...health,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    logger.error('Error en redis health check:', error);
    res.status(503).json({
      status: 'unhealthy',
      error: error.message
    });
  }
}

/**
 * Health check de Circuit Breakers
 * GET /health/circuit-breakers
 */
async function circuitBreakersHealth(req, res) {
  try {
    const services = {
      serpram: serpramService,
      ayt: aytService,
      esinfa: esinfaService,
      sercoamb: sercoambService
    };

    const health = HealthCheckService.checkCircuitBreakers(services);

    res.status(health.status === 'healthy' ? 200 : 200).json({
      ...health,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    logger.error('Error en circuit breakers health check:', error);
    res.status(503).json({
      status: 'unhealthy',
      error: error.message
    });
  }
}

/**
 * Health check de memoria y CPU
 * GET /health/system
 */
function systemHealth(req, res) {
  try {
    const memoryHealth = HealthCheckService.checkMemory();
    const cpuHealth = HealthCheckService.checkCPU();

    const status = memoryHealth.status === 'unhealthy' ? 'unhealthy' :
                  memoryHealth.status === 'degraded' ? 'degraded' : 'healthy';

    res.status(status === 'unhealthy' ? 503 : 200).json({
      status,
      timestamp: new Date().toISOString(),
      memory: memoryHealth,
      cpu: cpuHealth
    });
  } catch (error) {
    logger.error('Error en system health check:', error);
    res.status(503).json({
      status: 'unhealthy',
      error: error.message
    });
  }
}

/**
 * Health check de servicio específico
 * GET /health/service/:serviceName
 */
async function serviceHealth(req, res) {
  try {
    const { serviceName } = req.params;

    if (healthCheckService.services.size === 0) {
      await registerAllServices();
    }

    const health = await healthCheckService.checkService(serviceName);

    if (health.status === 'unknown') {
      return res.status(404).json(health);
    }

    res.status(health.status === 'healthy' ? 200 : 503).json(health);
  } catch (error) {
    logger.error(`Error en health check de ${req.params.serviceName}:`, error);
    res.status(503).json({
      status: 'unhealthy',
      error: error.message
    });
  }
}

/**
 * Liveness probe - Para Kubernetes
 * GET /health/live
 */
async function liveness(req, res) {
  res.status(200).json({
    status: 'alive',
    timestamp: new Date().toISOString()
  });
}

/**
 * Readiness probe - Para Kubernetes
 * GET /health/ready
 */
async function readiness(req, res) {
  try {
    // Verificar componentes críticos
    const writerHealth = await HealthCheckService.checkMySQLWriter(writerPool);
    const redisHealth = await HealthCheckService.checkRedis(stateStore);

    const ready = writerHealth.status === 'healthy' && redisHealth.status === 'healthy';

    res.status(ready ? 200 : 503).json({
      status: ready ? 'ready' : 'not ready',
      timestamp: new Date().toISOString(),
      checks: {
        database: writerHealth.status,
        redis: redisHealth.status
      }
    });
  } catch (error) {
    logger.error('Error en readiness probe:', error);
    res.status(503).json({
      status: 'not ready',
      error: error.message
    });
  }
}

/**
 * Registrar todos los servicios para health checks
 */
async function registerAllServices() {
  // MySQL Writer
  healthCheckService.registerService('mysql-writer', async () => {
    return await HealthCheckService.checkMySQLWriter(writerPool);
  });

  // MySQL Reader
  healthCheckService.registerService('mysql-reader', async () => {
    return await HealthCheckService.checkMySQLReader(readerPool);
  });

  // Redis
  healthCheckService.registerService('redis', async () => {
    return await HealthCheckService.checkRedis(stateStore);
  });

  // Circuit Breakers
  healthCheckService.registerService('circuit-breakers', () => {
    const services = {
      serpram: serpramService,
      ayt: aytService,
      esinfa: esinfaService,
      sercoamb: sercoambService
    };
    return HealthCheckService.checkCircuitBreakers(services);
  });

  // Memoria
  healthCheckService.registerService('memory', () => {
    return HealthCheckService.checkMemory();
  });

  // CPU
  healthCheckService.registerService('cpu', () => {
    return HealthCheckService.checkCPU();
  });

  logger.info('Todos los servicios de health check registrados');
}

module.exports = {
  basicHealth,
  detailedHealth,
  databaseHealth,
  redisHealth,
  circuitBreakersHealth,
  systemHealth,
  serviceHealth,
  liveness,
  readiness,
  registerAllServices
};
