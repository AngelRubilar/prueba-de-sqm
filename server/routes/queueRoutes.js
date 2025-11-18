const express = require('express');
const router = express.Router();
const queueController = require('../controllers/queueController');
const { verifyToken, requireRole } = require('../middleware/authMiddleware');

/**
 * Rutas para gestión y monitoreo de colas Bull
 * Requieren autenticación y rol admin
 */

// Health check de colas (sin autenticación)
router.get('/health', queueController.queuesHealthCheck);

// Estadísticas de todas las colas
router.get('/stats', verifyToken, queueController.getAllQueuesStats);

// Estadísticas de cola específica
router.get('/:queueName/stats', verifyToken, queueController.getQueueStats);

// Obtener jobs de una cola
router.get('/:queueName/jobs', verifyToken, queueController.getQueueJobs);

// Pausar cola (solo admin)
router.post('/:queueName/pause', verifyToken, requireRole('admin'), queueController.pauseQueue);

// Reanudar cola (solo admin)
router.post('/:queueName/resume', verifyToken, requireRole('admin'), queueController.resumeQueue);

// Limpiar jobs completados (solo admin)
router.post('/:queueName/clean/completed', verifyToken, requireRole('admin'), queueController.cleanCompletedJobs);

// Limpiar jobs fallidos (solo admin)
router.post('/:queueName/clean/failed', verifyToken, requireRole('admin'), queueController.cleanFailedJobs);

// Ejecutar sincronización SERPRAM manual (solo admin)
router.post('/serpram/run', verifyToken, requireRole('admin'), queueController.runSerpramSync);

// Ejecutar sincronización AYT manual (solo admin)
router.post('/ayt/run', verifyToken, requireRole('admin'), queueController.runAytSync);

module.exports = router;
