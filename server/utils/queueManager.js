const Queue = require('bull');
const logger = require('../config/logger');

/**
 * Queue Manager - Gestión centralizada de colas Bull
 *
 * Maneja todas las colas de trabajo del sistema usando Redis como backend.
 * Permite distribuir tareas programadas entre múltiples instancias del servidor.
 */

class QueueManager {
  constructor() {
    this.queues = new Map();
    this.redisConfig = {
      host: process.env.REDIS_HOST || 'localhost',
      port: process.env.REDIS_PORT || 6379,
      password: process.env.REDIS_PASSWORD || undefined,
      retryStrategy: (times) => {
        const delay = Math.min(times * 50, 2000);
        return delay;
      }
    };

    logger.info('QueueManager inicializado');
  }

  /**
   * Crear o obtener una cola
   * @param {string} name - Nombre de la cola
   * @param {Object} options - Opciones de la cola
   * @returns {Queue} Instancia de la cola
   */
  createQueue(name, options = {}) {
    if (this.queues.has(name)) {
      return this.queues.get(name);
    }

    const defaultOptions = {
      redis: this.redisConfig,
      defaultJobOptions: {
        attempts: 3,
        backoff: {
          type: 'exponential',
          delay: 2000
        },
        removeOnComplete: 100, // Mantener solo los últimos 100 completados
        removeOnFail: 500      // Mantener solo los últimos 500 fallidos
      },
      ...options
    };

    const queue = new Queue(name, defaultOptions);

    // Configurar event listeners
    this.setupQueueListeners(queue, name);

    this.queues.set(name, queue);
    logger.info(`Cola creada: ${name}`);

    return queue;
  }

  /**
   * Configurar listeners para eventos de la cola
   * @param {Queue} queue - Instancia de la cola
   * @param {string} name - Nombre de la cola
   */
  setupQueueListeners(queue, name) {
    queue.on('error', (error) => {
      logger.error(`Error en cola ${name}:`, error);
    });

    queue.on('waiting', (jobId) => {
      logger.debug(`Job ${jobId} esperando en cola ${name}`);
    });

    queue.on('active', (job) => {
      logger.info(`Job ${job.id} activo en cola ${name}`);
    });

    queue.on('completed', (job, result) => {
      logger.info(`Job ${job.id} completado en cola ${name}`, {
        duration: Date.now() - job.processedOn
      });
    });

    queue.on('failed', (job, err) => {
      logger.error(`Job ${job.id} falló en cola ${name}:`, {
        error: err.message,
        attempts: job.attemptsMade,
        maxAttempts: job.opts.attempts
      });
    });

    queue.on('stalled', (job) => {
      logger.warn(`Job ${job.id} estancado en cola ${name}`);
    });
  }

  /**
   * Obtener una cola existente
   * @param {string} name - Nombre de la cola
   * @returns {Queue|null} Instancia de la cola o null
   */
  getQueue(name) {
    return this.queues.get(name) || null;
  }

  /**
   * Agregar un job a una cola
   * @param {string} queueName - Nombre de la cola
   * @param {Object} data - Datos del job
   * @param {Object} options - Opciones del job
   * @returns {Promise<Job>} Job creado
   */
  async addJob(queueName, data, options = {}) {
    const queue = this.getQueue(queueName);
    if (!queue) {
      throw new Error(`Cola ${queueName} no existe`);
    }

    const job = await queue.add(data, options);
    logger.info(`Job ${job.id} agregado a cola ${queueName}`);
    return job;
  }

  /**
   * Agregar un job recurrente (cron)
   * @param {string} queueName - Nombre de la cola
   * @param {string} cronExpression - Expresión cron
   * @param {Object} data - Datos del job
   * @param {Object} options - Opciones adicionales
   * @returns {Promise<Job>} Job creado
   */
  async addRepeatingJob(queueName, cronExpression, data, options = {}) {
    const queue = this.getQueue(queueName);
    if (!queue) {
      throw new Error(`Cola ${queueName} no existe`);
    }

    const jobOptions = {
      repeat: {
        cron: cronExpression
      },
      ...options
    };

    const job = await queue.add(data, jobOptions);
    logger.info(`Job recurrente ${job.id} agregado a cola ${queueName}`, {
      cron: cronExpression
    });
    return job;
  }

  /**
   * Procesar jobs de una cola
   * @param {string} queueName - Nombre de la cola
   * @param {Function} processor - Función que procesa el job
   * @param {Object} options - Opciones de procesamiento
   */
  async process(queueName, processor, options = {}) {
    const queue = this.getQueue(queueName);
    if (!queue) {
      throw new Error(`Cola ${queueName} no existe`);
    }

    const concurrency = options.concurrency || 1;

    queue.process(concurrency, async (job) => {
      logger.info(`Procesando job ${job.id} de cola ${queueName}`);
      try {
        const result = await processor(job);
        return result;
      } catch (error) {
        logger.error(`Error procesando job ${job.id}:`, error);
        throw error;
      }
    });

    logger.info(`Procesador configurado para cola ${queueName}`, {
      concurrency
    });
  }

  /**
   * Obtener estadísticas de una cola
   * @param {string} queueName - Nombre de la cola
   * @returns {Promise<Object>} Estadísticas de la cola
   */
  async getQueueStats(queueName) {
    const queue = this.getQueue(queueName);
    if (!queue) {
      throw new Error(`Cola ${queueName} no existe`);
    }

    const [
      waiting,
      active,
      completed,
      failed,
      delayed,
      paused
    ] = await Promise.all([
      queue.getWaitingCount(),
      queue.getActiveCount(),
      queue.getCompletedCount(),
      queue.getFailedCount(),
      queue.getDelayedCount(),
      queue.getPausedCount()
    ]);

    return {
      name: queueName,
      waiting,
      active,
      completed,
      failed,
      delayed,
      paused,
      total: waiting + active + completed + failed + delayed + paused
    };
  }

  /**
   * Obtener estadísticas de todas las colas
   * @returns {Promise<Object>} Estadísticas de todas las colas
   */
  async getAllQueuesStats() {
    const stats = {};

    for (const [name, queue] of this.queues) {
      stats[name] = await this.getQueueStats(name);
    }

    return stats;
  }

  /**
   * Pausar una cola
   * @param {string} queueName - Nombre de la cola
   */
  async pauseQueue(queueName) {
    const queue = this.getQueue(queueName);
    if (!queue) {
      throw new Error(`Cola ${queueName} no existe`);
    }

    await queue.pause();
    logger.info(`Cola ${queueName} pausada`);
  }

  /**
   * Reanudar una cola
   * @param {string} queueName - Nombre de la cola
   */
  async resumeQueue(queueName) {
    const queue = this.getQueue(queueName);
    if (!queue) {
      throw new Error(`Cola ${queueName} no existe`);
    }

    await queue.resume();
    logger.info(`Cola ${queueName} reanudada`);
  }

  /**
   * Limpiar jobs completados de una cola
   * @param {string} queueName - Nombre de la cola
   * @param {number} gracePeriod - Período de gracia en ms
   */
  async cleanCompletedJobs(queueName, gracePeriod = 0) {
    const queue = this.getQueue(queueName);
    if (!queue) {
      throw new Error(`Cola ${queueName} no existe`);
    }

    const count = await queue.clean(gracePeriod, 'completed');
    logger.info(`${count} jobs completados eliminados de cola ${queueName}`);
    return count;
  }

  /**
   * Limpiar jobs fallidos de una cola
   * @param {string} queueName - Nombre de la cola
   * @param {number} gracePeriod - Período de gracia en ms
   */
  async cleanFailedJobs(queueName, gracePeriod = 0) {
    const queue = this.getQueue(queueName);
    if (!queue) {
      throw new Error(`Cola ${queueName} no existe`);
    }

    const count = await queue.clean(gracePeriod, 'failed');
    logger.info(`${count} jobs fallidos eliminados de cola ${queueName}`);
    return count;
  }

  /**
   * Obtener jobs de una cola por estado
   * @param {string} queueName - Nombre de la cola
   * @param {string} state - Estado (waiting, active, completed, failed, delayed)
   * @param {number} start - Índice inicial
   * @param {number} end - Índice final
   * @returns {Promise<Array>} Lista de jobs
   */
  async getJobs(queueName, state, start = 0, end = 10) {
    const queue = this.getQueue(queueName);
    if (!queue) {
      throw new Error(`Cola ${queueName} no existe`);
    }

    const jobs = await queue.getJobs([state], start, end);
    return jobs.map(job => ({
      id: job.id,
      data: job.data,
      progress: job.progress(),
      attemptsMade: job.attemptsMade,
      processedOn: job.processedOn,
      finishedOn: job.finishedOn,
      failedReason: job.failedReason
    }));
  }

  /**
   * Cerrar todas las colas
   */
  async closeAll() {
    logger.info('Cerrando todas las colas...');

    for (const [name, queue] of this.queues) {
      await queue.close();
      logger.info(`Cola ${name} cerrada`);
    }

    this.queues.clear();
    logger.info('Todas las colas cerradas');
  }

  /**
   * Health check de las colas
   * @returns {Promise<Object>} Estado de salud de las colas
   */
  async healthCheck() {
    try {
      const stats = await this.getAllQueuesStats();
      const hasFailedJobs = Object.values(stats).some(s => s.failed > 10);
      const hasPausedQueues = Object.values(stats).some(s => s.paused > 0);

      let status = 'healthy';
      let message = 'Todas las colas funcionando normalmente';

      if (hasPausedQueues) {
        status = 'degraded';
        message = 'Algunas colas están pausadas';
      } else if (hasFailedJobs) {
        status = 'degraded';
        message = 'Algunas colas tienen jobs fallidos';
      }

      return {
        status,
        message,
        details: stats
      };
    } catch (error) {
      return {
        status: 'unhealthy',
        message: `Error en health check: ${error.message}`
      };
    }
  }
}

// Exportar instancia singleton
const queueManager = new QueueManager();

module.exports = queueManager;
