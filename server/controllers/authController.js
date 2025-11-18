const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');

/**
 * Controlador de autenticación
 * Maneja login, logout y generación de tokens
 */
class AuthController {
  /**
   * Login de usuario
   * En esta implementación básica, validamos contra credenciales en .env
   * En producción, esto debería validar contra una base de datos
   */
  async login(req, res) {
    try {
      const { username, password } = req.body;

      // Validar que se envíen credenciales
      if (!username || !password) {
        return res.status(400).json({
          error: 'Credenciales incompletas',
          message: 'Debe proporcionar usuario y contraseña'
        });
      }

      // Obtener credenciales de variables de entorno
      const validUsername = process.env.API_USERNAME;
      const validPasswordHash = process.env.API_PASSWORD_HASH;

      if (!validUsername || !validPasswordHash) {
        console.error('Variables de autenticación no configuradas');
        return res.status(500).json({
          error: 'Error de configuración',
          message: 'El sistema de autenticación no está configurado correctamente'
        });
      }

      // Validar username
      if (username !== validUsername) {
        return res.status(401).json({
          error: 'Credenciales inválidas',
          message: 'Usuario o contraseña incorrectos'
        });
      }

      // Validar password
      const isValidPassword = await bcrypt.compare(password, validPasswordHash);

      if (!isValidPassword) {
        return res.status(401).json({
          error: 'Credenciales inválidas',
          message: 'Usuario o contraseña incorrectos'
        });
      }

      // Generar token JWT
      const token = jwt.sign(
        {
          username: username,
          role: 'admin', // Por defecto, el usuario es admin
          iat: Math.floor(Date.now() / 1000)
        },
        process.env.JWT_SECRET,
        {
          expiresIn: process.env.JWT_EXPIRES_IN || '24h'
        }
      );

      // Retornar token
      res.json({
        success: true,
        token: token,
        expiresIn: process.env.JWT_EXPIRES_IN || '24h',
        user: {
          username: username,
          role: 'admin'
        }
      });

    } catch (error) {
      console.error('Error en login:', error);
      res.status(500).json({
        error: 'Error en autenticación',
        message: 'Ocurrió un error al procesar la autenticación'
      });
    }
  }

  /**
   * Verificar si un token es válido
   */
  async verify(req, res) {
    // Si llegamos aquí, el middleware verifyToken ya validó el token
    res.json({
      success: true,
      user: req.user,
      message: 'Token válido'
    });
  }

  /**
   * Renovar token (refresh)
   */
  async refresh(req, res) {
    try {
      // El usuario ya está autenticado gracias al middleware
      const { username, role } = req.user;

      // Generar nuevo token
      const newToken = jwt.sign(
        {
          username: username,
          role: role,
          iat: Math.floor(Date.now() / 1000)
        },
        process.env.JWT_SECRET,
        {
          expiresIn: process.env.JWT_EXPIRES_IN || '24h'
        }
      );

      res.json({
        success: true,
        token: newToken,
        expiresIn: process.env.JWT_EXPIRES_IN || '24h'
      });

    } catch (error) {
      console.error('Error al renovar token:', error);
      res.status(500).json({
        error: 'Error al renovar token',
        message: 'Ocurrió un error al renovar el token'
      });
    }
  }

  /**
   * Utilidad para generar hash de contraseña
   * Solo para uso en desarrollo/setup inicial
   */
  async generatePasswordHash(req, res) {
    // Solo permitir en desarrollo
    if (process.env.NODE_ENV === 'production') {
      return res.status(403).json({
        error: 'No disponible en producción'
      });
    }

    const { password } = req.body;

    if (!password) {
      return res.status(400).json({
        error: 'Debe proporcionar una contraseña'
      });
    }

    const hash = await bcrypt.hash(password, 10);

    res.json({
      password: password,
      hash: hash,
      instructions: 'Copie el hash y agréguelo como API_PASSWORD_HASH en su archivo .env'
    });
  }
}

module.exports = new AuthController();
