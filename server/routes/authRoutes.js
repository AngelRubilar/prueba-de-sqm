const express = require('express');
const router = express.Router();
const authController = require('../controllers/authController');
const { verifyToken } = require('../middleware/authMiddleware');

/**
 * Rutas de autenticación
 * Estas rutas NO requieren autenticación previa
 */

// Login - Obtener token JWT
router.post('/login', authController.login);

// Verificar si un token es válido (requiere token)
router.get('/verify', verifyToken, authController.verify);

// Renovar token (requiere token válido)
router.post('/refresh', verifyToken, authController.refresh);

// Generar hash de contraseña (solo desarrollo)
router.post('/generate-hash', authController.generatePasswordHash);

module.exports = router;
