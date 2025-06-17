const axios = require('axios');
const moment = require('moment-timezone');
const nombreEstaciones = require('../config/nombreEstaciones');
const nombreVariables = require('../config/nombreVariables');
const authService = require('./authService');
const { serpramErrorHandler } = require('../errorHandlers');

class SerpramService {
  constructor() {
    this.dispositivos = ["Mejillones", "Sierra Gorda", "SQM Baquedano", "Maria Elena"];
    this.config = {
      baseURL: 'https://api2.serpram.cl:4321/air_ws/v1/api',
      token: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJkYXRlIjoiMjAyNS0wNC0yOCAxMTozNDoxNiIsInVzZXJfaWQiOjMxLCJkYXRhIjoxMzU3MDAwMDAwfQ.Qyl5Jbfrypav9zLE6wchfApXom6DUy8e0E-pyVkq93c'
    };
  }

  async consultarAPI(dispositivo) {
    try {
      const { estampaTiempoInicial, estampaTiempoFinal } = this.obtenerMarcasDeTiempo();
      
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

      return this.transformarRespuesta(response.data, dispositivo);
    } catch (error) {
      // Usar el errorHandler para manejar el error
      serpramErrorHandler.handleError(dispositivo, null, error);
      throw error;
    }
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

  obtenerMarcasDeTiempo() {
    const ahora = moment().tz('America/Santiago');
    const haceUnMinuto = ahora.clone().subtract(5, 'minutes');
    
    return {
      estampaTiempoInicial: haceUnMinuto.format('YYYY-MM-DDTHH:mm:ss'),
      estampaTiempoFinal: ahora.format('YYYY-MM-DDTHH:mm:ss')
    };
  }
}

module.exports = new SerpramService();