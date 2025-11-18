const queueManager = require('../utils/queueManager');
const { runSerpramSyncNow, getSerpramStats, pauseSerpramScheduler, resumeSerpramScheduler } = require('../schedulers/serpramScheduler');
const { runAytSyncNow, getAytStats, pauseAytScheduler, resumeAytScheduler } = require('../schedulers/aytScheduler');
const logger = require('../config/logger');

/**
 * Controlador para gestión y monitoreo de colas
 */

/**
 * Obtener estadísticas de todas las colas
 * GET /api/queues/stats
 */
async function getAllQueuesStats(req, res) {
  try {
    const stats = await queueManager.getAllQueuesStats();

    res.json({
      success: true,
      timestamp: new Date().toISOString(),
      queues: stats
    });
  } catch (error) {
    logger.error('Error obteniendo estadísticas de colas:', error);
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
}

/**
 * Obtener estadísticas de una cola específica
 * GET /api/queues/:queueName/stats
 */
async function getQueueStats(req, res) {
  try {
    const { queueName } = req.params;
    const stats = await queueManager.getQueueStats(queueName);

    res.json({
      success: true,
      timestamp: new Date().toISOString(),
      queue: stats
    });
  } catch (error) {
    logger.error(`Error obteniendo estadísticas de cola ${req.params.queueName}:`, error);
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
}

/**
 * Obtener jobs de una cola
 * GET /api/queues/:queueName/jobs?state=waiting&start=0&end=10
 */
async function getQueueJobs(req, res) {
  try {
    const { queueName } = req.params;
    const { state = 'waiting', start = 0, end = 10 } = req.query;

    const jobs = await queueManager.getJobs(
      queueName,
      state,
      parseInt(start),
      parseInt(end)
    );

    res.json({
      success: true,
      queue: queueName,
      state,
      jobs
    });
  } catch (error) {
    logger.error(`Error obteniendo jobs de cola ${req.params.queueName}:`, error);
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
}

/**
 * Pausar una cola
 * POST /api/queues/:queueName/pause
 */
async function pauseQueue(req, res) {
  try {
    const { queueName } = req.params;

    // Usar función específica del scheduler si existe
    if (queueName === 'serpram-sync') {
      await pauseSerpramScheduler();
    } else if (queueName === 'ayt-sync') {
      await pauseAytScheduler();
    } else {
      await queueManager.pauseQueue(queueName);
    }

    res.json({
      success: true,
      message: `Cola ${queueName} pausada`,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    logger.error(`Error pausando cola ${req.params.queueName}:`, error);
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
}

/**
 * Reanudar una cola
 * POST /api/queues/:queueName/resume
 */
async function resumeQueue(req, res) {
  try {
    const { queueName } = req.params;

    // Usar función específica del scheduler si existe
    if (queueName === 'serpram-sync') {
      await resumeSerpramScheduler();
    } else if (queueName === 'ayt-sync') {
      await resumeAytScheduler();
    } else {
      await queueManager.resumeQueue(queueName);
    }

    res.json({
      success: true,
      message: `Cola ${queueName} reanudada`,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    logger.error(`Error reanudando cola ${req.params.queueName}:`, error);
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
}

/**
 * Limpiar jobs completados de una cola
 * POST /api/queues/:queueName/clean/completed
 */
async function cleanCompletedJobs(req, res) {
  try {
    const { queueName } = req.params;
    const { gracePeriod = 0 } = req.body;

    const count = await queueManager.cleanCompletedJobs(queueName, gracePeriod);

    res.json({
      success: true,
      message: `${count} jobs completados eliminados`,
      count,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    logger.error(`Error limpiando jobs completados de ${req.params.queueName}:`, error);
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
}

/**
 * Limpiar jobs fallidos de una cola
 * POST /api/queues/:queueName/clean/failed
 */
async function cleanFailedJobs(req, res) {
  try {
    const { queueName } = req.params;
    const { gracePeriod = 0 } = req.body;

    const count = await queueManager.cleanFailedJobs(queueName, gracePeriod);

    res.json({
      success: true,
      message: `${count} jobs fallidos eliminados`,
      count,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    logger.error(`Error limpiando jobs fallidos de ${req.params.queueName}:`, error);
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
}

/**
 * Ejecutar sincronización SERPRAM manualmente
 * POST /api/queues/serpram/run
 */
async function runSerpramSync(req, res) {
  try {
    const job = await runSerpramSyncNow();

    res.json({
      success: true,
      message: 'Sincronización SERPRAM iniciada',
      jobId: job.id,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    logger.error('Error ejecutando sincronización SERPRAM:', error);
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
}

/**
 * Ejecutar sincronización AYT manualmente
 * POST /api/queues/ayt/run
 */
async function runAytSync(req, res) {
  try {
    const job = await runAytSyncNow();

    res.json({
      success: true,
      message: 'Sincronización AYT iniciada',
      jobId: job.id,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    logger.error('Error ejecutando sincronización AYT:', error);
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
}

/**
 * Health check de las colas
 * GET /api/queues/health
 */
async function queuesHealthCheck(req, res) {
  try {
    const health = await queueManager.healthCheck();

    const statusCode = health.status === 'healthy' ? 200 :
                      health.status === 'degraded' ? 200 : 503;

    res.status(statusCode).json({
      ...health,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    logger.error('Error en health check de colas:', error);
    res.status(503).json({
      status: 'unhealthy',
      error: error.message,
      timestamp: new Date().toISOString()
    });
  }
}

module.exports = {
  getAllQueuesStats,
  getQueueStats,
  getQueueJobs,
  pauseQueue,
  resumeQueue,
  cleanCompletedJobs,
  cleanFailedJobs,
  runSerpramSync,
  runAytSync,
  queuesHealthCheck
};
