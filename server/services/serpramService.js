const axios = require('axios');
const moment = require('moment-timezone');
const nombreEstaciones = require('../config/nombreEstaciones');
const nombreVariables = require('../config/nombreVariables');
const authService = require('./authService');
const apiErrorLogger = require('../utils/apiErrorLogger');

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
     //console.log('🔐 Usando token para Serpram:', token);
      
      // Construyo config usando el token dinámico
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

      /* console.log(`\nConsultando API Serpram para ${dispositivo}`);
      console.log('Configuración de la consulta:', {
        estampaTiempoInicial,
        estampaTiempoFinal,
        dispositivo,
        token: this.config.token
      }); */
      //console.log('🔐 Configuración de la consulta serpram:', config);

      const response = await axios.request(config);
      const resultado = response.data.resultado;

      // Debugging logs
      console.log('Respuesta completa:', response.data);
      console.log('Tipo de resultado:', typeof response.data.resultado);
      console.log('Resultado stringificado:', JSON.stringify(response.data.resultado));
      

      let shouldReportNoData = false;
          // verificamos si el resultado es un array
          // 2. Si es un array, verificar si está vacío O si sus contenidos indican falta de datos
      if (resultado.length === 0) {
        // Caso 1: El array está completamente vacío []
        console.log('El array está vacío. Debe reportarse como sin datos.');
        shouldReportNoData = true;

      } else {
        // Caso 2: El array no está vacío. Verificar si TODOS los elementos son objetos "vacíos"
        console.log(`El array contiene ${resultado.length} elementos. Verificando si todos son "datos vacíos" con logs detallados...`);

        const allElementsAreEmptyDataObjects = resultado.every((item, index) => {
            console.log(`--- Verificando elemento en índice ${index} ---`);

            // Verificar si es un objeto válido (no null, no array)
            const isObject = item !== null && typeof item === 'object' && !Array.isArray(item);
            console.log(`Elemento ${index}: ¿Es objeto válido (no null/array)? ${isObject}`);
            if (!isObject) return false; // Si no es objeto, este elemento falla la condición

            // Verificar 'dispositivoId'
            const hasDispositivoId = item.hasOwnProperty('dispositivoId');
            const isEmptyDispositivoId = hasDispositivoId && item.dispositivoId === "";
            console.log(`Elemento ${index}: ¿Tiene propiedad 'dispositivoId'? ${hasDispositivoId}. ¿Es ""? ${isEmptyDispositivoId} (Valor: "${item.dispositivoId}")`); // Log el valor real
            if (!isEmptyDispositivoId) return false; // Si falla, este elemento falla la condición

            // Verificar 'parametros'
            const hasParametros = item.hasOwnProperty('parametros');
            const isParametrosArray = hasParametros && Array.isArray(item.parametros);
            const isEmptyParametrosArray = isParametrosArray && item.parametros.length === 0;
            console.log(`Elemento ${index}: ¿Tiene propiedad 'parametros'? ${hasParametros}. ¿Es Array? ${isParametrosArray}. ¿Está vacío? ${isEmptyParametrosArray} (Longitud: ${item.parametros ? item.parametros.length : 'N/A'})`); // Log estado y longitud
            if (!isEmptyParametrosArray) return false; // Si falla, este elemento falla la condición

            // Si llegó hasta aquí, el elemento cumple todas las condiciones del patrón "vacío"
            console.log(`Elemento ${index} SÍ cumple el patrón de "datos vacíos".`);
            console.log('--- Fin verificación elemento ---');
            return true;
        });

        // Después de que .every() terminó
        if (allElementsAreEmptyDataObjects) {
            console.log('¡DEBUG: allElementsAreEmptyDataObjects es true! Todos los elementos cumplen el patrón.');
            shouldReportNoData = true; // Se establece a true porque todos eran "vacíos"
        } else {
            console.log('¡DEBUG: allElementsAreEmptyDataObjects es false! Al menos un elemento NO cumplió el patrón.');
        }
      }

      // --- El resto del código (el if final para reportar) se mantiene igual ---
      if (shouldReportNoData) {
        console.log('¡Reportando error: NO_DATA_RECEIVED!');
        apiErrorLogger.logConnectionError('Serpram', dispositivo, {
            errorType: 'NO_DATA_RECEIVED',
            message: 'La respuesta del servicio indica que no se recibieron datos (array vacío o array con elementos de "datos vacíos")',
            data: response.data
        });
      } else {
        console.log('No se cumplen las condiciones para reportar el error NO_DATA_RECEIVED.');
      }

      
      // Verificar el status de la respuesta
      //console.log(`Status de la respuesta: ${response.status}`);
      // Imprimir la respuesta completa para debug
      //console.log(`\nRespuesta de la API para ${dispositivo}:`);
      //console.log(JSON.stringify(response.data, null, 2));

      // Verificar si la respuesta indica un error de autenticación
      if (response.data.codigo === 'ERROR' || response.status === 401 || response.status === 403) {
        console.log('Error de autenticación detectado, intentando renovar token...');
        // Aquí podríamos implementar la renovación del token si es necesario
      }

      if (!response.data?.resultado) {
        apiErrorLogger.logEmptyResponse('Serpram', dispositivo);
      }

      return this.transformarRespuesta(response.data, dispositivo);
    } catch (error) {
      console.error(`Error al consultar API Serpram para ${dispositivo}:`, error.message);
      if (error.response) {
         // console.error('Respuesta del servidor:', error.response.data);
         // console.error('Status:', error.response.status);
         // console.error('Headers:', error.response.headers);
      }
      apiErrorLogger.logConnectionError('Serpram', dispositivo, error);
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
    const haceUnMinuto = ahora.clone().subtract(15, 'minutes');
    
    return {
      estampaTiempoInicial: haceUnMinuto.format('YYYY-MM-DDTHH:mm:ss'),
      estampaTiempoFinal: ahora.format('YYYY-MM-DDTHH:mm:ss')
    };
  }
}

module.exports = new SerpramService();