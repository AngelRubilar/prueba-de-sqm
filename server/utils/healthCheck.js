const logger = require('../config/logger');

/**
 * Health Check Comprehensivo para el Sistema
 *
 * Verifica el estado de todos los componentes críticos:
 * - Base de datos MySQL (reader y writer)
 * - Redis
 * - Circuit Breakers de APIs externas
 * - Memoria y CPU
 */

class HealthCheckService {
  constructor() {
    this.services = new Map();
    this.startTime = Date.now();
  }

  /**
   * Registrar un servicio para monitorear
   * @param {string} name - Nombre del servicio
   * @param {Function} checkFunction - Función async que retorna estado de salud
   */
  registerService(name, checkFunction) {
    this.services.set(name, checkFunction);
    logger.info(`Health check registrado para: ${name}`);
  }

  /**
   * Verificar salud de un servicio específico
   * @param {string} name - Nombre del servicio
   * @returns {Object} Estado de salud del servicio
   */
  async checkService(name) {
    const checkFunction = this.services.get(name);

    if (!checkFunction) {
      return {
        name,
        status: 'unknown',
        message: 'Servicio no registrado'
      };
    }

    try {
      const startTime = Date.now();
      const result = await checkFunction();
      const duration = Date.now() - startTime;

      return {
        name,
        status: result.status || 'healthy',
        message: result.message || 'OK',
        details: result.details || {},
        responseTime: `${duration}ms`,
        timestamp: new Date().toISOString()
      };
    } catch (error) {
      logger.error(`Health check falló para ${name}:`, error);
      return {
        name,
        status: 'unhealthy',
        message: error.message,
        timestamp: new Date().toISOString()
      };
    }
  }

  /**
   * Verificar salud de todos los servicios
   * @returns {Object} Estado de salud completo del sistema
   */
  async checkAll() {
    const checks = {};

    for (const [name] of this.services) {
      checks[name] = await this.checkService(name);
    }

    // Determinar estado general
    const allHealthy = Object.values(checks).every(
      check => check.status === 'healthy'
    );
    const someUnhealthy = Object.values(checks).some(
      check => check.status === 'unhealthy'
    );

    const overallStatus = allHealthy ? 'healthy' : (someUnhealthy ? 'unhealthy' : 'degraded');

    return {
      status: overallStatus,
      uptime: this.getUptime(),
      timestamp: new Date().toISOString(),
      checks
    };
  }

  /**
   * Obtener tiempo de actividad del sistema
   * @returns {string} Tiempo de actividad formateado
   */
  getUptime() {
    const uptime = Date.now() - this.startTime;
    const seconds = Math.floor((uptime / 1000) % 60);
    const minutes = Math.floor((uptime / (1000 * 60)) % 60);
    const hours = Math.floor((uptime / (1000 * 60 * 60)) % 24);
    const days = Math.floor(uptime / (1000 * 60 * 60 * 24));

    const parts = [];
    if (days > 0) parts.push(`${days}d`);
    if (hours > 0) parts.push(`${hours}h`);
    if (minutes > 0) parts.push(`${minutes}m`);
    parts.push(`${seconds}s`);

    return parts.join(' ');
  }

  /**
   * Health check para MySQL (Writer Pool)
   */
  static async checkMySQLWriter(writerPool) {
    try {
      const [result] = await writerPool.query('SELECT 1 as health');

      return {
        status: 'healthy',
        message: 'MySQL Writer conectado',
        details: {
          connections: writerPool.pool._allConnections.length,
          freeConnections: writerPool.pool._freeConnections.length,
          queuedConnections: writerPool.pool._connectionQueue.length
        }
      };
    } catch (error) {
      return {
        status: 'unhealthy',
        message: `MySQL Writer error: ${error.message}`
      };
    }
  }

  /**
   * Health check para MySQL (Reader Pool)
   */
  static async checkMySQLReader(readerPool) {
    try {
      const [result] = await readerPool.query('SELECT 1 as health');

      return {
        status: 'healthy',
        message: 'MySQL Reader conectado',
        details: {
          connections: readerPool.pool._allConnections.length,
          freeConnections: readerPool.pool._freeConnections.length,
          queuedConnections: readerPool.pool._connectionQueue.length
        }
      };
    } catch (error) {
      return {
        status: 'unhealthy',
        message: `MySQL Reader error: ${error.message}`
      };
    }
  }

  /**
   * Health check para Redis
   */
  static async checkRedis(stateStore) {
    try {
      const health = await stateStore.healthCheck();

      return {
        status: health.status === 'healthy' ? 'healthy' : 'unhealthy',
        message: health.connected ? 'Redis conectado' : 'Redis desconectado',
        details: {
          connected: health.connected,
          timestamp: health.timestamp
        }
      };
    } catch (error) {
      return {
        status: 'unhealthy',
        message: `Redis error: ${error.message}`
      };
    }
  }

  /**
   * Health check para Circuit Breakers
   */
  static checkCircuitBreakers(services) {
    const breakers = [];
    let allClosed = true;

    for (const [name, service] of Object.entries(services)) {
      if (typeof service.getCircuitBreakerStats === 'function') {
        const stats = service.getCircuitBreakerStats();

        // Manejar SERCOAMB que tiene múltiples breakers
        if (name === 'sercoamb' && stats.tamentica && stats.victoria) {
          breakers.push(stats.tamentica);
          breakers.push(stats.victoria);

          if (stats.tamentica.state === 'OPEN' || stats.victoria.state === 'OPEN') {
            allClosed = false;
          }
        } else {
          breakers.push(stats);
          if (stats.state === 'OPEN') {
            allClosed = false;
          }
        }
      }
    }

    const openBreakers = breakers.filter(b => b.state === 'OPEN');
    const halfOpenBreakers = breakers.filter(b => b.state === 'HALF_OPEN');

    let status = 'healthy';
    let message = 'Todos los Circuit Breakers funcionando normalmente';

    if (openBreakers.length > 0) {
      status = 'degraded';
      message = `${openBreakers.length} Circuit Breaker(s) ABIERTOS`;
    } else if (halfOpenBreakers.length > 0) {
      status = 'degraded';
      message = `${halfOpenBreakers.length} Circuit Breaker(s) en recuperación`;
    }

    return {
      status,
      message,
      details: {
        total: breakers.length,
        open: openBreakers.length,
        halfOpen: halfOpenBreakers.length,
        closed: breakers.filter(b => b.state === 'CLOSED').length,
        breakers: breakers.map(b => ({
          name: b.name,
          state: b.state,
          fires: b.fires,
          successes: b.successes,
          failures: b.failures,
          successRate: b.fires > 0 ? `${((b.successes / b.fires) * 100).toFixed(2)}%` : 'N/A'
        }))
      }
    };
  }

  /**
   * Health check para uso de memoria
   */
  static checkMemory() {
    const usage = process.memoryUsage();
    const totalMemory = usage.heapTotal;
    const usedMemory = usage.heapUsed;
    const usagePercent = (usedMemory / totalMemory) * 100;

    let status = 'healthy';
    let message = 'Uso de memoria normal';

    if (usagePercent > 90) {
      status = 'unhealthy';
      message = 'Uso de memoria crítico';
    } else if (usagePercent > 75) {
      status = 'degraded';
      message = 'Uso de memoria elevado';
    }

    return {
      status,
      message,
      details: {
        heapUsed: `${(usedMemory / 1024 / 1024).toFixed(2)} MB`,
        heapTotal: `${(totalMemory / 1024 / 1024).toFixed(2)} MB`,
        usagePercent: `${usagePercent.toFixed(2)}%`,
        rss: `${(usage.rss / 1024 / 1024).toFixed(2)} MB`,
        external: `${(usage.external / 1024 / 1024).toFixed(2)} MB`
      }
    };
  }

  /**
   * Health check para CPU
   */
  static checkCPU() {
    const usage = process.cpuUsage();
    const uptime = process.uptime();

    return {
      status: 'healthy',
      message: 'CPU funcionando normalmente',
      details: {
        user: `${(usage.user / 1000000).toFixed(2)}s`,
        system: `${(usage.system / 1000000).toFixed(2)}s`,
        uptime: `${uptime.toFixed(2)}s`
      }
    };
  }

  /**
   * Health check básico para verificar que el servidor está vivo
   */
  static async checkBasic() {
    return {
      status: 'healthy',
      message: 'Servidor funcionando',
      details: {
        nodeVersion: process.version,
        platform: process.platform,
        pid: process.pid
      }
    };
  }
}

// Exportar instancia singleton
const healthCheckService = new HealthCheckService();

module.exports = {
  healthCheckService,
  HealthCheckService
};
