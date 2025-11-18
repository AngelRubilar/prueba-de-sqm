const queueManager = require('../utils/queueManager');
const aytController = require('../controllers/aytController');
const logger = require('../config/logger');

/**
 * Scheduler para AYT usando Bull Queue
 * Ejecuta cada 1 minuto de forma distribuida
 */

const QUEUE_NAME = 'ayt-sync';
const CRON_EXPRESSION = '* * * * *'; // Cada 1 minuto

/**
 * Inicializar el scheduler de AYT
 */
async function initAytScheduler() {
  try {
    // Crear la cola
    const queue = queueManager.createQueue(QUEUE_NAME, {
      defaultJobOptions: {
        attempts: 3,
        backoff: {
          type: 'exponential',
          delay: 3000
        },
        removeOnComplete: 100,
        removeOnFail: 200
      }
    });

    // Configurar el procesador
    await queueManager.process(QUEUE_NAME, processAytJob, {
      concurrency: 1 // Solo un job a la vez
    });

    // Agregar job recurrente
    await queueManager.addRepeatingJob(
      QUEUE_NAME,
      CRON_EXPRESSION,
      { type: 'ayt-sync' },
      {
        jobId: 'ayt-recurring', // ID fijo para evitar duplicados
        repeat: {
          cron: CRON_EXPRESSION
        }
      }
    );

    logger.info('Scheduler AYT inicializado', {
      queue: QUEUE_NAME,
      cron: CRON_EXPRESSION
    });

    return queue;
  } catch (error) {
    logger.error('Error inicializando scheduler AYT:', error);
    throw error;
  }
}

/**
 * Procesar job de sincronización AYT
 * @param {Job} job - Job de Bull
 * @returns {Promise<Object>} Resultado del procesamiento
 */
async function processAytJob(job) {
  const startTime = Date.now();
  logger.info(`Iniciando sincronización AYT - Job ${job.id}`);

  try {
    // Actualizar progreso
    await job.progress(20);

    // Ejecutar sincronización de todas las estaciones AYT
    await aytController.obtenerDatosEstacionesProgramado();

    // Actualizar progreso
    await job.progress(100);

    const duration = Date.now() - startTime;
    logger.info(`Sincronización AYT completada - Job ${job.id}`, {
      duration: `${duration}ms`
    });

    return {
      success: true,
      duration
    };
  } catch (error) {
    const duration = Date.now() - startTime;
    logger.error(`Error en sincronización AYT - Job ${job.id}:`, {
      error: error.message,
      duration: `${duration}ms`
    });

    throw error;
  }
}

/**
 * Ejecutar sincronización AYT manualmente
 * @returns {Promise<Job>} Job creado
 */
async function runAytSyncNow() {
  logger.info('Ejecutando sincronización AYT manual');

  const job = await queueManager.addJob(
    QUEUE_NAME,
    { type: 'ayt-manual-sync' },
    {
      priority: 1, // Alta prioridad para ejecuciones manuales
      attempts: 1
    }
  );

  return job;
}

/**
 * Obtener estadísticas del scheduler AYT
 * @returns {Promise<Object>} Estadísticas
 */
async function getAytStats() {
  return await queueManager.getQueueStats(QUEUE_NAME);
}

/**
 * Pausar el scheduler AYT
 */
async function pauseAytScheduler() {
  await queueManager.pauseQueue(QUEUE_NAME);
  logger.info('Scheduler AYT pausado');
}

/**
 * Reanudar el scheduler AYT
 */
async function resumeAytScheduler() {
  await queueManager.resumeQueue(QUEUE_NAME);
  logger.info('Scheduler AYT reanudado');
}

module.exports = {
  initAytScheduler,
  runAytSyncNow,
  getAytStats,
  pauseAytScheduler,
  resumeAytScheduler,
  QUEUE_NAME
};
