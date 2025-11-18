const axios = require('axios');
const moment = require('moment-timezone');
const nombreEstaciones = require('../config/nombreEstaciones');
const nombreVariables = require('../config/nombreVariables');
const authService = require('./authService');
const { serpramErrorHandler } = require('../errorHandlers');
const { createApiCircuitBreaker } = require('../utils/circuitBreaker');
const logger = require('../config/logger');

class SerpramService {
  constructor() {
    this.dispositivos = ["Mejillones", "Sierra Gorda", "SQM Baquedano", "Maria Elena"];
    this.config = {
      baseURL: process.env.SERPRAM_API_URL || 'https://api.serpram.cl/air_ws/v1/api',
      token: process.env.SERPRAM_TOKEN
    };

    // Validar que el token esté configurado
    if (!this.config.token) {
      throw new Error('SERPRAM_TOKEN no está configurado en las variables de entorno');
    }

    // Crear Circuit Breaker para la API
    this.circuitBreaker = createApiCircuitBreaker(
      'SERPRAM',
      this._makeApiRequest.bind(this),
      {
        timeout: 20000, // 20 segundos para SERPRAM
        errorThresholdPercentage: 50,
        resetTimeout: 60000 // 1 minuto
      }
    );

    logger.info('SerpramService inicializado con Circuit Breaker');
  }

  /**
   * Método privado que realiza la llamada real a la API
   * Protegido por Circuit Breaker
   */
  async _makeApiRequest(dispositivo, estampaTiempoInicial, estampaTiempoFinal) {
    // Obtengo token dinámico de authService
    const token = await authService.getToken();

    const config = {
      method: 'get',
      url: `${this.config.baseURL}/getHistorico`,
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      data: {
        estampaTiempoInicial,
        estampaTiempoFinal,
        tipoMedicion: 1,
        consulta: [{ dispositivoId: dispositivo }]
      }
    };

    const response = await axios.request(config);

    // Usar el errorHandler para manejar la respuesta
    serpramErrorHandler.handleError(dispositivo, response);

    return response.data;
  }

  /**
   * Consultar API de SERPRAM con Circuit Breaker
   */
  async consultarAPI(dispositivo, timestampDesde = null) {
    try {
      const { estampaTiempoInicial, estampaTiempoFinal } = this.obtenerMarcasDeTiempo(timestampDesde);

      // Usar Circuit Breaker para proteger la llamada
      const data = await this.circuitBreaker.fire(dispositivo, estampaTiempoInicial, estampaTiempoFinal);

      // Si el Circuit Breaker devolvió fallback
      if (data?.fallback) {
        logger.warn(`SERPRAM API no disponible para ${dispositivo}, usando fallback`);
        return [];
      }

      return this.transformarRespuesta(data, dispositivo);
    } catch (error) {
      // Usar el errorHandler para manejar el error
      serpramErrorHandler.handleError(dispositivo, null, error);

      // Si el circuito está abierto, retornar array vacío en lugar de fallar
      if (error.message && error.message.includes('Breaker is open')) {
        logger.warn(`Circuit Breaker abierto para SERPRAM, retornando datos vacíos`);
        return [];
      }

      throw error;
    }
  }

  /**
   * Obtener estadísticas del Circuit Breaker
   */
  getCircuitBreakerStats() {
    const stats = this.circuitBreaker.stats;
    return {
      name: 'SERPRAM',
      state: this.circuitBreaker.opened ? 'OPEN' : (this.circuitBreaker.halfOpen ? 'HALF_OPEN' : 'CLOSED'),
      fires: stats.fires,
      successes: stats.successes,
      failures: stats.failures,
      rejects: stats.rejects,
      timeouts: stats.timeouts
    };
  }

  transformarRespuesta(data, dispositivo) {
    if (!data?.resultado) {
      console.log(`No hay resultados para ${dispositivo}`);
      return [];
    }

    const nuevoNombreEstacion = nombreEstaciones[dispositivo] || dispositivo;
    const datosArray = [];
    const parametrosProcesados = new Map();

    // Verificar si resultado es un array
    const resultados = Array.isArray(data.resultado) ? data.resultado : [data.resultado];

    // Procesar solo el primer resultado para cada dispositivo
    const resultadoUnico = resultados.find(r => r.dispositivoId === dispositivo);
    
    if (!resultadoUnico?.parametros) {
      console.log(`No hay parámetros para ${dispositivo}`);
      return [];
    }

    // Asegurarnos de que parametros sea un array
    const parametros = Array.isArray(resultadoUnico.parametros) ? 
      resultadoUnico.parametros : 
      [resultadoUnico.parametros];

    // Procesar parámetros en un solo bucle
    for (const parametro of parametros) {
      const { nombre, valor, estampaTiempo } = parametro;
      
      if (!nombre || valor === undefined || !estampaTiempo) {
        continue;
      }

      const nombreEstandarizado = nombreVariables[nombre] || nombre;
      const fecha = moment(estampaTiempo).tz('America/Santiago').format('YYYY-MM-DD HH:mm:ss');
      const clave = `${fecha}-${nombreEstandarizado}`;

      // Solo agregar si no hemos procesado este parámetro
      if (!parametrosProcesados.has(clave)) {
        datosArray.push([fecha, nuevoNombreEstacion, nombreEstandarizado, valor]);
        parametrosProcesados.set(clave, true);
      }
    }

    console.log(`Procesados ${datosArray.length} registros únicos para ${dispositivo}`);
    return datosArray;
  }

  obtenerMarcasDeTiempo(timestampDesde = null) {
    const ahora = moment().tz('America/Santiago');
    
    // La API de Serpram tiene una hora menos que Chile, así que SIEMPRE consultamos con hora actual - 1 hora
    const horaApi = ahora.clone().subtract(1, 'hour');
    
    // Siempre usamos un rango de 15 minutos desde la hora ajustada de la API
    const estampaTiempoInicial = horaApi.clone().subtract(15, 'minutes').format('YYYY-MM-DDTHH:mm:ss');
    const estampaTiempoFinal = horaApi.format('YYYY-MM-DDTHH:mm:ss');
    
    console.log(`🕐 Consultando Serpram desde: ${estampaTiempoInicial} hasta: ${estampaTiempoFinal}`);
    console.log(`📅 Hora Chile actual: ${ahora.format('YYYY-MM-DD HH:mm:ss')} | Hora API ajustada: ${horaApi.format('YYYY-MM-DD HH:mm:ss')}`);
    console.log(`ℹ️  API consulta 1 hora antes que Chile para encontrar datos disponibles`);
    
    return {
      estampaTiempoInicial,
      estampaTiempoFinal
    };
  }
}

module.exports = new SerpramService();