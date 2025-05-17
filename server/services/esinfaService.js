const axios = require('axios');
const moment = require('moment-timezone');
const nombreEstaciones = require('../config/nombreEstaciones');
const nombreVariables = require('../config/nombreVariables');
const esinfaAuthService = require('./esinfaAuthService');
const apiErrorLogger = require('../utils/apiErrorLogger');

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
      console.log('Consultando API Esinfa...');
      
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

      //console.log('Enviando petición a la API Esinfa...');
      let response = await axios.request(config);

      if (response.status === 500) {
        //console.log('Error 500, intentando renovar token...');
        const newToken = await esinfaAuthService.obtenerNuevoToken();
        config.headers['Authorization'] = `Bearer ${newToken}`;
        response = await axios.request(config);
      }

      if (!response.data || response.data.length === 0) {
        apiErrorLogger.logEmptyResponse('Esinfa', 'Hospital');
      }

      return this.transformarRespuesta(response.data);
    } catch (error) {
      apiErrorLogger.logConnectionError('Esinfa', 'Hospital', error);
      if (error.response && error.response.status === 500) {
        try {
          //console.log('Error 500, intentando renovar token...');
          const newToken = await esinfaAuthService.obtenerNuevoToken();
          this.config.headers['Authorization'] = `Bearer ${newToken}`;
          const response = await axios.request(this.config);
          return this.transformarRespuesta(response.data);
        } catch (retryError) {
          //console.error('Error al reintentar la solicitud:', retryError.message);
          throw retryError;
        }
      }
      //console.error('Error al consultar API Esinfa:', error.message);
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

      console.log(`Procesando ${data.length} estaciones de Esinfa`);

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

      console.log(`Datos transformados exitosamente: ${datosArray.length} registros`);
      return datosArray;
    } catch (error) {
      console.error('Error al transformar respuesta:', error.message);
      return datosArray;
    }
  }

  async fetchAllDevices() {
    try {
      console.log('Iniciando obtención de datos de todas las estaciones Esinfa...');
      const datosArray = await this.consultarAPI();
      console.log(`Datos obtenidos exitosamente: ${datosArray.length} registros`);
      return datosArray;
    } catch (error) {
      console.error('Error al obtener datos de todas las estaciones:', error.message);
      throw error;
    }
  }
}

module.exports = new EsinfaService();