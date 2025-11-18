const axios = require('axios');
const moment = require('moment-timezone');
const nombreEstaciones = require('../config/nombreEstaciones');
const nombreVariables = require('../config/nombreVariables');
const esinfaAuthService = require('./esinfaAuthService');
const { esinfaErrorHandler } = require('../errorHandlers');
const { createApiCircuitBreaker } = require('../utils/circuitBreaker');
const logger = require('../config/logger');

class EsinfaService {
  constructor() {
    this.config = {
      baseURL: 'https://airsqm.weboard.cl/station/',
      headers: {
        'Authorization': 'Bearer '
      }
    };

    // Crear Circuit Breaker para la API ESINFA
    this.circuitBreaker = createApiCircuitBreaker(
      'ESINFA',
      this._makeApiRequest.bind(this),
      {
        timeout: 15000, // 15 segundos
        errorThresholdPercentage: 50,
        resetTimeout: 60000 // 1 minuto
      }
    );

    logger.info('EsinfaService inicializado con Circuit Breaker');
  }

  /**
   * Método privado que realiza la llamada real a la API ESINFA
   * Protegido por Circuit Breaker
   */
  async _makeApiRequest(config) {
    const response = await axios.request(config);

    // Usar el errorHandler para manejar la respuesta
    esinfaErrorHandler.handleError(response);

    return response.data;
  }

  /**
   * Consultar API de ESINFA con Circuit Breaker
   */
  async consultarAPI() {
    try {
      // Obtener token actualizado
      const token = await esinfaAuthService.getToken();

      let config = {
        method: 'get',
        maxBodyLength: Infinity,
        url: this.config.baseURL,
        headers: {
          ...this.config.headers,
          'Authorization': `Bearer ${token}`
        }
      };

      // Usar Circuit Breaker para proteger la llamada
      const data = await this.circuitBreaker.fire(config);

      // Si el Circuit Breaker devolvió fallback
      if (data?.fallback) {
        logger.warn(`ESINFA API no disponible, usando fallback`);
        return [];
      }

      return this.transformarRespuesta(data);
    } catch (error) {
      // Si el circuito está abierto, retornar array vacío
      if (error.message && error.message.includes('Breaker is open')) {
        logger.warn(`Circuit Breaker abierto para ESINFA, retornando datos vacíos`);
        return [];
      }

      // Manejo de error 500 con renovación de token
      if (error.response && error.response.status === 500) {
        try {
          const newToken = await esinfaAuthService.obtenerNuevoToken();
          const config = {
            method: 'get',
            maxBodyLength: Infinity,
            url: this.config.baseURL,
            headers: {
              ...this.config.headers,
              'Authorization': `Bearer ${newToken}`
            }
          };
          // Reintentar con Circuit Breaker
          const data = await this.circuitBreaker.fire(config);
          return this.transformarRespuesta(data);
        } catch (retryError) {
          // Usar el errorHandler para manejar el error de reintento
          esinfaErrorHandler.handleError(null, retryError);
          return [];
        }
      }

      // Usar el errorHandler para manejar el error original
      esinfaErrorHandler.handleError(null, error);
      return [];
    }
  }

  /**
   * Obtener estadísticas del Circuit Breaker
   */
  getCircuitBreakerStats() {
    const stats = this.circuitBreaker.stats;
    return {
      name: 'ESINFA',
      state: this.circuitBreaker.opened ? 'OPEN' : (this.circuitBreaker.halfOpen ? 'HALF_OPEN' : 'CLOSED'),
      fires: stats.fires,
      successes: stats.successes,
      failures: stats.failures,
      rejects: stats.rejects,
      timeouts: stats.timeouts
    };
  }

  transformarRespuesta(data) {
    const datosArray = [];
    try {
      if (!data || !Array.isArray(data)) {
        console.error('Respuesta inválida de la API:', data);
        return datosArray;
      }

      for (const resultado of data) {
        try {
          const dispositivo = resultado.station;
          const nuevoNombreEstacion = nombreEstaciones[dispositivo] || dispositivo;
          const estampaTiempo = moment.tz(resultado.date_capture, 'America/Santiago')
            .add(1, 'hours')
            .format('YYYY-MM-DD HH:mm:ss');

          if (!resultado.data || !Array.isArray(resultado.data)) {
            console.error(`Datos inválidos para la estación ${dispositivo}:`, resultado);
            continue;
          }

          for (const parametro of resultado.data) {
            try {
              if (!parametro.name || parametro.value === undefined) {
                console.error(`Parámetro inválido para ${dispositivo}:`, parametro);
                continue;
              }

              const nombreOriginal = parametro.name;
              const nombreEstandarizado = nombreVariables[nombreOriginal] || nombreOriginal;
              const valor = parametro.value;

              if (estampaTiempo === 'Invalid date') {
                console.error(`Fecha inválida para el parámetro: ${JSON.stringify(parametro)}`);
                continue;
              }

              datosArray.push([estampaTiempo, nuevoNombreEstacion, nombreEstandarizado, valor]);
            } catch (paramError) {
              console.error(`Error al procesar parámetro para ${dispositivo}:`, paramError.message);
              continue;
            }
          }
        } catch (stationError) {
          console.error(`Error al procesar estación:`, stationError.message);
          continue;
        }
      }

      return datosArray;
    } catch (error) {
      console.error('Error al transformar respuesta:', error.message);
      return datosArray;
    }
  }

  async fetchAllDevices() {
    try {
      const datosArray = await this.consultarAPI();
      return datosArray;
    } catch (error) {
      throw error;
    }
  }
}

module.exports = new EsinfaService();