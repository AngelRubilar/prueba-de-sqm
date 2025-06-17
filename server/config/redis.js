const Redis = require('ioredis');

// Configura la conexión a Redis. Usa variables de entorno para flexibilidad.
const redisClient = new Redis({
  host: process.env.REDIS_HOST || 'localhost',
  port: process.env.REDIS_PORT || 6379,
  password: process.env.REDIS_PASSWORD,
  retryStrategy: function(times) {
    const delay = Math.min(times * 1000, 10000);
    console.log(`Intentando reconectar a Redis en ${delay}ms...`);
    return delay;
  },
  maxRetriesPerRequest: 5,
  enableReadyCheck: true,
  connectTimeout: 20000,
  commandTimeout: 10000,
  reconnectOnError: function(err) {
    const targetError = 'READONLY';
    if (err.message.includes(targetError)) {
      return true;
    }
    return false;
  }
});

// Agregar la función de diagnóstico aquí
async function diagnoseRedisConfig() {
    try {
        const config = await redisClient.config('GET', '*');
        console.log('[Redis] Configuración actual:', {
            maxmemory: config.find(c => c[0] === 'maxmemory')[1],
            maxmemoryPolicy: config.find(c => c[0] === 'maxmemory-policy')[1],
            appendonly: config.find(c => c[0] === 'appendonly')[1],
            appendfsync: config.find(c => c[0] === 'appendfsync')[1]
        });

        const info = await redisClient.info();
        console.log('[Redis] Información del servidor:', {
            version: info.redis_version,
            mode: info.redis_mode,
            os: info.os,
            arch: info.arch_bits,
            processId: info.process_id
        });

        const memory = await redisClient.info('memory');
        console.log('[Redis] Estado de memoria:', {
            usedMemory: formatBytes(memory.used_memory),
            usedMemoryPeak: formatBytes(memory.used_memory_peak),
            maxmemory: formatBytes(memory.maxmemory),
            maxmemoryPolicy: memory.maxmemory_policy
        });
    } catch (error) {
        console.error('[Redis] Error al obtener diagnóstico:', error);
    }
}

// Función auxiliar para formatear bytes
function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

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
  // Ejecutar diagnóstico cuando Redis está listo
  diagnoseRedisConfig();
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

// Exportar tanto el cliente como las funciones de verificación
module.exports = {
  redisClient,
  checkRedisConnection,
  diagnoseRedisConfig  // Exportar la función de diagnóstico
};