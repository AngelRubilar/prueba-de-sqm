const queueManager = require('../utils/queueManager');
const serpramController = require('../controllers/serpramController');
const { cargarTimestampSerpramAsync, guardarTimestampSerpramAsync } = require('../store');
const logger = require('../config/logger');

/**
 * Scheduler para SERPRAM usando Bull Queue
 * Ejecuta cada 5 minutos de forma distribuida
 */

const QUEUE_NAME = 'serpram-sync';
const CRON_EXPRESSION = '*/5 * * * *'; // Cada 5 minutos

/**
 * Inicializar el scheduler de SERPRAM
 */
async function initSerpramScheduler() {
  try {
    // Crear la cola
    const queue = queueManager.createQueue(QUEUE_NAME, {
      defaultJobOptions: {
        attempts: 3,
        backoff: {
          type: 'exponential',
          delay: 5000
        },
        removeOnComplete: 50,
        removeOnFail: 100
      }
    });

    // Configurar el procesador
    await queueManager.process(QUEUE_NAME, processSerpramJob, {
      concurrency: 1 // Solo un job a la vez
    });

    // Agregar job recurrente
    await queueManager.addRepeatingJob(
      QUEUE_NAME,
      CRON_EXPRESSION,
      { type: 'serpram-sync' },
      {
        jobId: 'serpram-recurring', // ID fijo para evitar duplicados
        repeat: {
          cron: CRON_EXPRESSION
        }
      }
    );

    logger.info('Scheduler SERPRAM inicializado', {
      queue: QUEUE_NAME,
      cron: CRON_EXPRESSION
    });

    return queue;
  } catch (error) {
    logger.error('Error inicializando scheduler SERPRAM:', error);
    throw error;
  }
}

/**
 * Procesar job de sincronización SERPRAM
 * @param {Job} job - Job de Bull
 * @returns {Promise<Object>} Resultado del procesamiento
 */
async function processSerpramJob(job) {
  const startTime = Date.now();
  logger.info(`Iniciando sincronización SERPRAM - Job ${job.id}`);

  try {
    // Cargar último timestamp
    const since = await cargarTimestampSerpramAsync();
    logger.info(`SERPRAM - consultando desde: ${since}`);

    // Actualizar progreso
    await job.progress(20);

    // Realizar consulta
    const resultados = await serpramController.realizarConsulta({ since });

    // Actualizar progreso
    await job.progress(60);

    if (!Array.isArray(resultados)) {
      logger.warn('La consulta SERPRAM no devolvió un array');
      return {
        success: false,
        message: 'Respuesta inválida de la API',
        duration: Date.now() - startTime
      };
    }

    // Guardar nuevo timestamp si hay resultados
    if (resultados.length > 0) {
      const ultimoRegistro = resultados[resultados.length - 1];
      const [nuevoTs] = ultimoRegistro;
      await guardarTimestampSerpramAsync(nuevoTs);

      logger.info(`SERPRAM - nuevo timestamp guardado: ${nuevoTs}`);
    }

    // Actualizar progreso
    await job.progress(100);

    const duration = Date.now() - startTime;
    logger.info(`Sincronización SERPRAM completada - Job ${job.id}`, {
      registros: resultados.length,
      duration: `${duration}ms`
    });

    return {
      success: true,
      registros: resultados.length,
      duration
    };
  } catch (error) {
    const duration = Date.now() - startTime;
    logger.error(`Error en sincronización SERPRAM - Job ${job.id}:`, {
      error: error.message,
      duration: `${duration}ms`
    });

    throw error;
  }
}

/**
 * Ejecutar sincronización SERPRAM manualmente
 * @returns {Promise<Job>} Job creado
 */
async function runSerpramSyncNow() {
  logger.info('Ejecutando sincronización SERPRAM manual');

  const job = await queueManager.addJob(
    QUEUE_NAME,
    { type: 'serpram-manual-sync' },
    {
      priority: 1, // Alta prioridad para ejecuciones manuales
      attempts: 1
    }
  );

  return job;
}

/**
 * Obtener estadísticas del scheduler SERPRAM
 * @returns {Promise<Object>} Estadísticas
 */
async function getSerpramStats() {
  return await queueManager.getQueueStats(QUEUE_NAME);
}

/**
 * Pausar el scheduler SERPRAM
 */
async function pauseSerpramScheduler() {
  await queueManager.pauseQueue(QUEUE_NAME);
  logger.info('Scheduler SERPRAM pausado');
}

/**
 * Reanudar el scheduler SERPRAM
 */
async function resumeSerpramScheduler() {
  await queueManager.resumeQueue(QUEUE_NAME);
  logger.info('Scheduler SERPRAM reanudado');
}

module.exports = {
  initSerpramScheduler,
  runSerpramSyncNow,
  getSerpramStats,
  pauseSerpramScheduler,
  resumeSerpramScheduler,
  QUEUE_NAME
};
