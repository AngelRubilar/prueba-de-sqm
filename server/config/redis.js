const Redis = require('ioredis');

// Configura la conexión a Redis. Usa variables de entorno para flexibilidad.
const redisClient = new Redis({
  host: process.env.REDIS_HOST || 'localhost',
  port: process.env.REDIS_PORT || 6379,
  password: process.env.REDIS_PASSWORD,
  retryStrategy: function(times) {
    // Intentar reconectar cada 1 segundo, hasta un máximo de 10 segundos
    const delay = Math.min(times * 1000, 10000);
    console.log(`Intentando reconectar a Redis en ${delay}ms...`);
    return delay;
  },
  maxRetriesPerRequest: 3,
  enableReadyCheck: true,
  connectTimeout: 10000, // 10 segundos de timeout para la conexión inicial
  commandTimeout: 5000,  // 5 segundos de timeout para comandos
  reconnectOnError: function(err) {
    const targetError = 'READONLY';
    if (err.message.includes(targetError)) {
      return true; // Reconectar cuando el error es READONLY
    }
    return false;
  }
});

// Mejorar el manejo de eventos con más información
redisClient.on('error', (err) => {
  console.error('Error de conexión con Redis:', err);
  console.error('Detalles del error:', {
    code: err.code,
    message: err.message,
    stack: err.stack
  });
});

redisClient.on('connect', () => {
  console.log('Conectado a Redis exitosamente en:', {
    host: process.env.REDIS_HOST || 'redis',
    port: process.env.REDIS_PORT || 6379
  });
});

redisClient.on('ready', () => {
  console.log('Redis está listo para recibir comandos');
});

redisClient.on('reconnecting', (params) => {
  console.log('Reconectando a Redis...', {
    attempt: params.attempt,
    delay: params.delay
  });
});

redisClient.on('end', () => {
  console.log('Conexión a Redis cerrada');
});

// Función mejorada para verificar la conexión
async function checkRedisConnection() {
  try {
    const startTime = Date.now();
    await redisClient.ping();
    const endTime = Date.now();
    
    console.log('Redis está respondiendo correctamente', {
      responseTime: `${endTime - startTime}ms`,
      status: 'connected',
      host: process.env.REDIS_HOST || 'redis',
      port: process.env.REDIS_PORT || 6379
    });
    
    // Verificar si podemos escribir y leer
    const testKey = 'redis_test_connection';
    await redisClient.set(testKey, 'test');
    const value = await redisClient.get(testKey);
    await redisClient.del(testKey);
    
    if (value === 'test') {
      console.log('Redis: Prueba de escritura/lectura exitosa');
    }
    
    return true;
  } catch (error) {
    console.error('Error al verificar conexión con Redis:', {
      error: error.message,
      code: error.code,
      host: process.env.REDIS_HOST || 'redis',
      port: process.env.REDIS_PORT || 6379
    });
    return false;
  }
}

// Verificar la conexión al iniciar y cada 5 minutos
checkRedisConnection();
setInterval(checkRedisConnection, 5 * 60 * 1000);

// Exportar tanto el cliente como la función de verificación
module.exports = {
  redisClient,
  checkRedisConnection
};