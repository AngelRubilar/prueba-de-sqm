const Redis = require('ioredis');
const moment = require('moment-timezone');

/**
 * StateStore - Gestión de estado persistente con Redis
 * Reemplaza el almacenamiento en archivo JSON por un sistema más robusto
 *
 * Ventajas sobre store.js anterior:
 * - Operaciones asíncronas (no bloquea event loop)
 * - Thread-safe (sin race conditions)
 * - Escalable horizontalmente
 * - Transacciones atómicas
 * - Backup y recuperación automáticos
 */
class StateStore {
  constructor() {
    this.redis = null;
    this.connected = false;
    this.prefix = 'state:';
  }

  /**
   * Inicializar conexión a Redis
   */
  async connect() {
    if (this.connected) {
      return;
    }

    try {
      this.redis = new Redis({
        host: process.env.REDIS_HOST || 'localhost',
        port: process.env.REDIS_PORT || 6379,
        password: process.env.REDIS_PASSWORD || undefined,
        retryStrategy: (times) => {
          const delay = Math.min(times * 50, 2000);
          return delay;
        },
        maxRetriesPerRequest: 3,
        enableReadyCheck: true,
        lazyConnect: true
      });

      // Eventos de conexión
      this.redis.on('connect', () => {
        console.log('✅ StateStore conectado a Redis');
        this.connected = true;
      });

      this.redis.on('error', (err) => {
        console.error('❌ Error en conexión Redis StateStore:', err.message);
        this.connected = false;
      });

      this.redis.on('close', () => {
        console.log('⚠️  Conexión Redis StateStore cerrada');
        this.connected = false;
      });

      this.redis.on('reconnecting', () => {
        console.log('🔄 Reconectando a Redis StateStore...');
      });

      // Conectar
      await this.redis.connect();

      console.log('✅ StateStore inicializado correctamente');
    } catch (error) {
      console.error('❌ Error al inicializar StateStore:', error);
      throw error;
    }
  }

  /**
   * Cerrar conexión a Redis
   */
  async disconnect() {
    if (this.redis && this.connected) {
      await this.redis.quit();
      this.connected = false;
      console.log('StateStore desconectado');
    }
  }

  /**
   * Obtener último timestamp de SERPRAM
   */
  async getLastTimestampSerpram() {
    try {
      const key = `${this.prefix}serpram:lastTimestamp`;
      const value = await this.redis.get(key);

      if (value) {
        return value;
      }

      // Si no existe, retornar timestamp por defecto (últimos 15 minutos)
      const defaultTimestamp = moment()
        .tz('America/Santiago')
        .subtract(15, 'minutes')
        .toISOString();

      console.log(`📊 No hay timestamp previo de SERPRAM, usando: ${defaultTimestamp}`);
      return defaultTimestamp;
    } catch (error) {
      console.error('Error al obtener timestamp SERPRAM:', error);
      // Fallback a timestamp por defecto
      return moment()
        .tz('America/Santiago')
        .subtract(15, 'minutes')
        .toISOString();
    }
  }

  /**
   * Guardar último timestamp de SERPRAM
   */
  async setLastTimestampSerpram(timestamp) {
    try {
      const key = `${this.prefix}serpram:lastTimestamp`;
      await this.redis.set(key, timestamp);
      console.log(`✅ Timestamp SERPRAM guardado: ${timestamp}`);
    } catch (error) {
      console.error('Error al guardar timestamp SERPRAM:', error);
      throw error;
    }
  }

  /**
   * Obtener último timestamp por fuente de datos
   * @param {string} source - Nombre de la fuente (serpram, ayt, esinfa, etc.)
   */
  async getLastTimestamp(source) {
    try {
      const key = `${this.prefix}${source}:lastTimestamp`;
      const value = await this.redis.get(key);

      if (value) {
        return value;
      }

      // Si no existe, retornar timestamp por defecto
      const defaultTimestamp = moment()
        .tz('America/Santiago')
        .subtract(15, 'minutes')
        .toISOString();

      console.log(`📊 No hay timestamp previo de ${source}, usando: ${defaultTimestamp}`);
      return defaultTimestamp;
    } catch (error) {
      console.error(`Error al obtener timestamp de ${source}:`, error);
      return moment()
        .tz('America/Santiago')
        .subtract(15, 'minutes')
        .toISOString();
    }
  }

  /**
   * Guardar último timestamp por fuente de datos
   * @param {string} source - Nombre de la fuente (serpram, ayt, esinfa, etc.)
   * @param {string} timestamp - Timestamp a guardar
   */
  async setLastTimestamp(source, timestamp) {
    try {
      const key = `${this.prefix}${source}:lastTimestamp`;
      await this.redis.set(key, timestamp);
      console.log(`✅ Timestamp ${source} guardado: ${timestamp}`);
    } catch (error) {
      console.error(`Error al guardar timestamp de ${source}:`, error);
      throw error;
    }
  }

  /**
   * Guardar valor genérico en el store
   * @param {string} key - Clave
   * @param {any} value - Valor (será JSON.stringify si es objeto)
   * @param {number} ttl - Tiempo de vida en segundos (opcional)
   */
  async set(key, value, ttl = null) {
    try {
      const fullKey = `${this.prefix}${key}`;
      const serializedValue = typeof value === 'object' ? JSON.stringify(value) : String(value);

      if (ttl) {
        await this.redis.setex(fullKey, ttl, serializedValue);
      } else {
        await this.redis.set(fullKey, serializedValue);
      }
    } catch (error) {
      console.error(`Error al guardar ${key}:`, error);
      throw error;
    }
  }

  /**
   * Obtener valor genérico del store
   * @param {string} key - Clave
   * @param {boolean} parseJSON - Si debe parsear como JSON
   */
  async get(key, parseJSON = false) {
    try {
      const fullKey = `${this.prefix}${key}`;
      const value = await this.redis.get(fullKey);

      if (!value) {
        return null;
      }

      return parseJSON ? JSON.parse(value) : value;
    } catch (error) {
      console.error(`Error al obtener ${key}:`, error);
      return null;
    }
  }

  /**
   * Eliminar clave del store
   */
  async delete(key) {
    try {
      const fullKey = `${this.prefix}${key}`;
      await this.redis.del(fullKey);
    } catch (error) {
      console.error(`Error al eliminar ${key}:`, error);
      throw error;
    }
  }

  /**
   * Verificar si una clave existe
   */
  async exists(key) {
    try {
      const fullKey = `${this.prefix}${key}`;
      const result = await this.redis.exists(fullKey);
      return result === 1;
    } catch (error) {
      console.error(`Error al verificar existencia de ${key}:`, error);
      return false;
    }
  }

  /**
   * Obtener todas las claves con un patrón
   */
  async keys(pattern = '*') {
    try {
      const fullPattern = `${this.prefix}${pattern}`;
      const keys = await this.redis.keys(fullPattern);
      // Remover el prefix de las claves retornadas
      return keys.map(key => key.replace(this.prefix, ''));
    } catch (error) {
      console.error('Error al obtener claves:', error);
      return [];
    }
  }

  /**
   * Migrar datos desde store.json antiguo (solo una vez)
   */
  async migrateFromFileStore(filePath) {
    try {
      const fs = require('fs');
      if (!fs.existsSync(filePath)) {
        console.log('📋 No hay archivo store.json para migrar');
        return;
      }

      const data = JSON.parse(fs.readFileSync(filePath, 'utf8'));

      if (data.lastTimestampSerpram) {
        await this.setLastTimestampSerpram(data.lastTimestampSerpram);
        console.log('✅ Migrado timestamp de SERPRAM desde archivo');
      }

      // Migrar otros datos si existen
      for (const [key, value] of Object.entries(data)) {
        if (key !== 'lastTimestampSerpram') {
          await this.set(key, value);
          console.log(`✅ Migrado ${key} desde archivo`);
        }
      }

      console.log('✅ Migración completada desde store.json');
    } catch (error) {
      console.error('Error en migración:', error);
    }
  }

  /**
   * Obtener estadísticas del store
   */
  async getStats() {
    try {
      const keys = await this.keys();
      const info = await this.redis.info('stats');

      return {
        totalKeys: keys.length,
        keys: keys,
        redisInfo: info,
        connected: this.connected
      };
    } catch (error) {
      console.error('Error al obtener estadísticas:', error);
      return { error: error.message };
    }
  }

  /**
   * Health check
   */
  async healthCheck() {
    try {
      await this.redis.ping();
      return {
        status: 'healthy',
        connected: this.connected,
        timestamp: new Date().toISOString()
      };
    } catch (error) {
      return {
        status: 'unhealthy',
        connected: false,
        error: error.message,
        timestamp: new Date().toISOString()
      };
    }
  }
}

// Exportar instancia singleton
const stateStore = new StateStore();

module.exports = stateStore;
