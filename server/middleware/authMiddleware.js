const jwt = require('jsonwebtoken');

/**
 * Middleware de autenticación JWT
 * Verifica que el request tenga un token válido en el header Authorization
 */
function verifyToken(req, res, next) {
  // Obtener token del header Authorization
  const authHeader = req.headers.authorization;

  if (!authHeader) {
    return res.status(401).json({
      error: 'Acceso denegado',
      message: 'No se proporcionó token de autenticación'
    });
  }

  // El formato esperado es: "Bearer TOKEN"
  const parts = authHeader.split(' ');

  if (parts.length !== 2 || parts[0] !== 'Bearer') {
    return res.status(401).json({
      error: 'Formato de token inválido',
      message: 'El formato debe ser: Bearer [token]'
    });
  }

  const token = parts[1];

  try {
    // Verificar el token
    const decoded = jwt.verify(token, process.env.JWT_SECRET);

    // Agregar información del usuario al request
    req.user = decoded;

    // Continuar con el siguiente middleware
    next();
  } catch (error) {
    if (error.name === 'TokenExpiredError') {
      return res.status(401).json({
        error: 'Token expirado',
        message: 'El token ha expirado, por favor inicie sesión nuevamente'
      });
    }

    if (error.name === 'JsonWebTokenError') {
      return res.status(401).json({
        error: 'Token inválido',
        message: 'El token proporcionado no es válido'
      });
    }

    return res.status(500).json({
      error: 'Error al verificar token',
      message: 'Ocurrió un error al procesar la autenticación'
    });
  }
}

/**
 * Middleware opcional de autenticación
 * Si hay token lo valida, pero si no hay permite continuar
 */
function optionalAuth(req, res, next) {
  const authHeader = req.headers.authorization;

  if (!authHeader) {
    return next();
  }

  const parts = authHeader.split(' ');

  if (parts.length !== 2 || parts[0] !== 'Bearer') {
    return next();
  }

  const token = parts[1];

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    req.user = decoded;
  } catch (error) {
    // Ignorar errores en auth opcional
  }

  next();
}

/**
 * Middleware para verificar roles específicos
 * Debe usarse DESPUÉS de verifyToken
 */
function requireRole(...allowedRoles) {
  return (req, res, next) => {
    if (!req.user) {
      return res.status(401).json({
        error: 'No autenticado',
        message: 'Debe estar autenticado para acceder a este recurso'
      });
    }

    if (!allowedRoles.includes(req.user.role)) {
      return res.status(403).json({
        error: 'Acceso denegado',
        message: `Se requiere uno de los siguientes roles: ${allowedRoles.join(', ')}`
      });
    }

    next();
  };
}

module.exports = {
  verifyToken,
  optionalAuth,
  requireRole
};
