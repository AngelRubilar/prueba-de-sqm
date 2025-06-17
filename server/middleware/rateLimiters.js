const rateLimit = require('express-rate-limit');

// Limitador para datos de gráficos (PM10, SO2, viento)
const graphDataLimiter = rateLimit({
  windowMs: 60 * 1000, // 1 minuto
  max: 30, // 30 peticiones por minuto
  standardHeaders: true,
  legacyHeaders: false,
  message: {
    status: 429,
    message: 'Demasiadas peticiones de datos de gráficos, por favor espere un momento'
  }
});

// Limitador para datos de múltiples variables
const multipleVariablesLimiter = rateLimit({
  windowMs: 60 * 1000, // 1 minuto
  max: 20, // 20 peticiones por minuto
  standardHeaders: true,
  legacyHeaders: false,
  message: {
    status: 429,
    message: 'Demasiadas peticiones de datos múltiples, por favor espere un momento'
  }
});

// Limitador para reportes
const reportLimiter = rateLimit({
  windowMs: 60 * 60 * 1000, // 1 hora
  max: 5, // 5 peticiones por hora
  standardHeaders: true,
  legacyHeaders: false,
  message: {
    status: 429,
    message: 'Demasiadas peticiones de reportes, por favor espere una hora'
  }
});

// Limitador para pruebas de email
const emailTestLimiter = rateLimit({
  windowMs: 60 * 60 * 1000, // 1 hora
  max: 3, // 3 peticiones por hora
  standardHeaders: true,
  legacyHeaders: false,
  message: {
    status: 429,
    message: 'Demasiadas peticiones de prueba de email, por favor espere una hora'
  }
});

// Limitador para datos de pronóstico
const forecastLimiter = rateLimit({
  windowMs: 5 * 60 * 1000, // 5 minutos
  max: 30, // 30 peticiones por 5 minutos
  message: {
    error: 'Demasiadas peticiones de pronóstico. Por favor, intente más tarde.'
  }
});


module.exports = {
  graphDataLimiter,
  multipleVariablesLimiter,
  reportLimiter,
  emailTestLimiter,
  forecastLimiter
};