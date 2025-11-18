const express = require('express');
const router = express.Router();
const healthController = require('../controllers/healthController');

/**
 * Rutas de Health Check
 * Estas rutas NO requieren autenticación para facilitar monitoreo
 */

// Health check básico - Solo verifica que el servidor está vivo
router.get('/', healthController.basicHealth);

// Health check detallado - Verifica todos los componentes
router.get('/detailed', healthController.detailedHealth);

// Health checks específicos por componente
router.get('/database', healthController.databaseHealth);
router.get('/redis', healthController.redisHealth);
router.get('/circuit-breakers', healthController.circuitBreakersHealth);
router.get('/system', healthController.systemHealth);

// Health check de servicio específico
router.get('/service/:serviceName', healthController.serviceHealth);

// Probes para Kubernetes
router.get('/live', healthController.liveness);
router.get('/ready', healthController.readiness);

module.exports = router;
