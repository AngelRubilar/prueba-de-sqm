const axios = require('axios');
const moment = require('moment-timezone');
const nombreEstaciones = require('../config/nombreEstaciones');
const nombreVariables = require('../config/nombreVariables');
const esinfaAuthService = require('./esinfaAuthService');
const { esinfaErrorHandler } = require('../errorHandlers');

class EsinfaService {
  constructor() {
    this.config = {
      baseURL: 'https://airsqm.weboard.cl/station/',
      headers: {
        'Authorization': 'Bearer '
      }
    };
  }

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

      let response = await axios.request(config);

      if (response.status === 500) {
        const newToken = await esinfaAuthService.obtenerNuevoToken();
        config.headers['Authorization'] = `Bearer ${newToken}`;
        response = await axios.request(config);
      }

      // Usar el errorHandler para manejar la respuesta
      esinfaErrorHandler.handleError(response);

      return this.transformarRespuesta(response.data);
    } catch (error) {
      if (error.response && error.response.status === 500) {
        try {
          const newToken = await esinfaAuthService.obtenerNuevoToken();
          this.config.headers['Authorization'] = `Bearer ${newToken}`;
          const response = await axios.request(this.config);
          return this.transformarRespuesta(response.data);
        } catch (retryError) {
          // Usar el errorHandler para manejar el error de reintento
          esinfaErrorHandler.handleError(null, retryError);
          throw retryError;
        }
      }
      // Usar el errorHandler para manejar el error original
      esinfaErrorHandler.handleError(null, error);
      throw error;
    }
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