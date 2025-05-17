const Redis = require('ioredis');

// Configura la conexión a Redis. Usa variables de entorno para flexibilidad.
const redisClient = new Redis({
  host: process.env.REDIS_HOST || 'localhost', // Host del servicio Redis (puede ser el nombre del servicio en Docker Compose)
  port: process.env.REDIS_PORT || 6379,       // Puerto de Redis
  password: process.env.REDIS_PASSWORD, // Contraseña de Redis
  // Agrega aquí otras opciones si son necesarias (contraseña, etc.)
});

redisClient.on('error', (err) => {
  console.error('Error de conexión con Redis:', err);
});

redisClient.on('connect', () => {
  console.log('Conectado a Redis');
});

module.exports = redisClient; 