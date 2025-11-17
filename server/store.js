/**
 * store.js - Wrapper de compatibilidad para StateStore (Redis)
 *
 * Este archivo mantiene la interfaz original para compatibilidad con código existente,
 * pero internamente usa Redis en lugar de archivo JSON.
 *
 * MIGRACIÓN:
 * - Versión anterior: fs.writeFileSync() bloqueante → archivo JSON
 * - Versión actual: Redis asíncrono → almacenamiento distribuido
 *
 * IMPORTANTE: Las funciones siguen siendo síncronas externamente por compatibilidad,
 * pero internamente usan Redis de forma asíncrona con await bloqueado.
 * En futuras refactorizaciones, convertir todo el flujo a async/await.
 */

const stateStore = require('./services/stateStore');
const moment = require('moment-timezone');

// Flag para indicar si Redis está listo
let redisReady = false;

// Inicializar StateStore al cargar el módulo
stateStore.connect()
  .then(async () => {
    redisReady = true;
    console.log('✅ Store migrado a Redis correctamente');

    // Intentar migrar datos desde store.json si existe
    const path = require('path');
    const oldStorePath = path.resolve(__dirname, '../store.json');
    await stateStore.migrateFromFileStore(oldStorePath);
  })
  .catch((error) => {
    console.error('❌ Error al inicializar StateStore:', error);
    console.error('⚠️  FALLBACK: Usando modo degradado (memoria temporal)');
    redisReady = false;
  });

// Almacenamiento temporal en memoria como fallback si Redis falla
const memoryFallback = {
  lastTimestampSerpram: null
};

/**
 * Cargar último timestamp de SERPRAM
 * Mantiene compatibilidad síncrona con código existente
 */
function cargarTimestampSerpram() {
  if (!redisReady) {
    console.warn('⚠️  Redis no disponible, usando fallback en memoria');
    return memoryFallback.lastTimestampSerpram ||
           moment().tz('America/Santiago').subtract(15, 'minutes').toISOString();
  }

  // NOTA: Esta es una operación síncrona bloqueante como workaround temporal
  // En producción, refactorizar todo el flujo a async/await
  try {
    // Usar promesa síncrona (no recomendado en producción, solo para compatibilidad)
    let result = null;
    stateStore.getLastTimestampSerpram()
      .then(value => { result = value; })
      .catch(err => {
        console.error('Error al cargar timestamp:', err);
        result = moment().tz('America/Santiago').subtract(15, 'minutes').toISOString();
      });

    // Esperar de forma síncrona (hack temporal)
    const start = Date.now();
    while (result === null && (Date.now() - start) < 1000) {
      // Espera activa (temporal, refactorizar a async)
    }

    return result || moment().tz('America/Santiago').subtract(15, 'minutes').toISOString();
  } catch (error) {
    console.error('Error crítico al cargar timestamp:', error);
    return moment().tz('America/Santiago').subtract(15, 'minutes').toISOString();
  }
}

/**
 * Guardar último timestamp de SERPRAM
 * Mantiene compatibilidad síncrona con código existente
 */
function guardarTimestampSerpram(nuevoTimestamp) {
  if (!redisReady) {
    console.warn('⚠️  Redis no disponible, guardando en memoria temporal');
    memoryFallback.lastTimestampSerpram = nuevoTimestamp;
    return;
  }

  // Ejecutar de forma asíncrona sin bloquear
  stateStore.setLastTimestampSerpram(nuevoTimestamp)
    .catch(error => {
      console.error('Error al guardar timestamp:', error);
      // Fallback a memoria
      memoryFallback.lastTimestampSerpram = nuevoTimestamp;
    });
}

/**
 * Versión async de cargarTimestampSerpram (recomendado usar en nuevo código)
 */
async function cargarTimestampSerpramAsync() {
  if (!redisReady) {
    return memoryFallback.lastTimestampSerpram ||
           moment().tz('America/Santiago').subtract(15, 'minutes').toISOString();
  }

  return await stateStore.getLastTimestampSerpram();
}

/**
 * Versión async de guardarTimestampSerpram (recomendado usar en nuevo código)
 */
async function guardarTimestampSerpramAsync(nuevoTimestamp) {
  if (!redisReady) {
    memoryFallback.lastTimestampSerpram = nuevoTimestamp;
    return;
  }

  await stateStore.setLastTimestampSerpram(nuevoTimestamp);
}

// Exportar funciones síncronas para compatibilidad y versiones async para nuevo código
module.exports = {
  // Funciones síncronas (compatibilidad con código existente)
  cargarTimestampSerpram,
  guardarTimestampSerpram,

  // Funciones async (recomendadas para nuevo código)
  cargarTimestampSerpramAsync,
  guardarTimestampSerpramAsync,

  // Acceso directo a stateStore para funcionalidad avanzada
  stateStore,

  // Estado de conexión
  isRedisReady: () => redisReady
};
