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
      baseURL: process.env.SERPRAM_API_URL || 'https://api.serpram.cl/air_ws/v1/api',
      token: process.env.SERPRAM_TOKEN
    };

    // Validar que el token esté configurado
    if (!this.config.token) {
      throw new Error('SERPRAM_TOKEN no está configurado en las variables de entorno');
    }
  }

  async consultarAPI(dispositivo, timestampDesde = null) {
    try {
      const { estampaTiempoInicial, estampaTiempoFinal } = this.obtenerMarcasDeTiempo(timestampDesde);
      
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