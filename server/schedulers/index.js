const { initSerpramScheduler } = require('./serpramScheduler');
const { initAytScheduler } = require('./aytScheduler');
const logger = require('../config/logger');

/**
 * Inicializar todos los schedulers del sistema
 */
async function initAllSchedulers() {
  logger.info('Inicializando todos los schedulers...');

  try {
    // Inicializar scheduler SERPRAM (cada 5 minutos)
    await initSerpramScheduler();
    logger.info('✅ Scheduler SERPRAM inicializado');

    // Inicializar scheduler AYT (cada 1 minuto)
    await initAytScheduler();
    logger.info('✅ Scheduler AYT inicializado');

    logger.info('🎉 Todos los schedulers inicializados correctamente');
  } catch (error) {
    logger.error('❌ Error inicializando schedulers:', error);
    throw error;
  }
}

module.exports = {
  initAllSchedulers
};
