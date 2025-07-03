import axios from 'axios';

const BASE = process.env.REACT_APP_API_URL || '/api';

const api = axios.create({
  baseURL: BASE,
  timeout: 10000,
});

// Interceptor para manejar errores de rate limit
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 429) {
      const retryAfter = error.response.headers['retry-after'] || 60;
      const message = error.response.data?.message || 'Demasiadas peticiones, por favor espere un momento';
      
      const rateLimitError = new Error(message);
      rateLimitError.isRateLimit = true;
      rateLimitError.retryAfter = retryAfter;
      
      return Promise.reject(rateLimitError);
    }
    return Promise.reject(error);
  }
);

/**
 * Obtiene datos de PM10 desde el backend
 * @returns {Promise<Array>} Array de registros [[fecha, estación, var, valor], ...]
 */
export function fetchPM10Data() {
  return api.get('/datos-PM10')
    .then(res => res.data);
}

/**
 * Obtiene datos de viento desde el backend
 * @returns {Promise<Array>} Array de registros [[timestamp, valor], ...]
 */
export function fetchVientoData() {
  return api.get('/datos-viento')
    .then(res => res.data);
}

/**
 * Obtiene datos de SO2 desde el backend
 * @returns {Promise<Array>} Array de registros [{timestamp, station_name, valor}, ...]
 */
export function fetchSO2Data() {
  return api.get('/datos-SO2')
    .then(res => res.data);
}

/**
 * Obtiene datos de múltiples variables desde el backend
 * @returns {Promise<Array>} Array de registros [{timestamp, station_name, variable_name, valor}, ...]
 */
export function fetchVariablesData() {
  return api.get('/datos-variables')
    .then(res => res.data);
}

export const fetchForecastData = async () => {
  try {
    //console.log('=== INICIANDO PETICIÓN DE PRONÓSTICO ===');
    const response = await fetch('/api/datos-pronostico-SO2');
    //console.log('Respuesta del servidor:', response.status, response.statusText);
    
    if (!response.ok) {
      const errorData = await response.json();
      //console.error('Error detallado:', errorData);
      throw new Error(`Error al obtener datos del pronóstico: ${JSON.stringify(errorData)}`);
    }
    
    const dataByStation = await response.json();
    //console.log('Datos recibidos del servidor por estación:', dataByStation);
    //console.log('Estaciones disponibles:', Object.keys(dataByStation));
    
    // Transformar los datos al formato que espera Highcharts, separados por estación
    const transformedData = {};
    
    Object.keys(dataByStation).forEach(station => {
      const stationData = dataByStation[station];
      //console.log(`Procesando estación ${station}:`, stationData.length, 'registros');
      
      if (stationData && stationData.length > 0) {
        transformedData[station] = {
          forecast: stationData.map(d => [new Date(d.ds).getTime(), d.yhat]),
          real: stationData.map(d => [new Date(d.ds).getTime(), d.y]),
          range: stationData.map(d => [new Date(d.ds).getTime(), d.yhat_lower, d.yhat_upper])
        };
        /* console.log(`${station} - Datos transformados:`, {
          forecast: transformedData[station].forecast.length,
          real: transformedData[station].real.length,
          range: transformedData[station].range.length
        }); */
      } else {
        //console.log(`No hay datos para la estación ${station}`);
        transformedData[station] = {
          forecast: [],
          real: [],
          range: []
        };
      }
    });
    
    //console.log('=== DATOS TRANSFORMADOS FINALES ===');
   /*  Object.keys(transformedData).forEach(station => {
       console.log(`${station}:`, {
        forecast: transformedData[station].forecast.length,
        real: transformedData[station].real.length,
        range: transformedData[station].range.length
      }); 
    }); */
    
    return transformedData;
  } catch (error) {
    console.error('Error completo en fetchForecastData:', error);
    return {
      'Huara': { forecast: [], real: [], range: [] },
      'Victoria': { forecast: [], real: [], range: [] },
      'Colonia Pintados': { forecast: [], real: [], range: [] }
    };
  }
};