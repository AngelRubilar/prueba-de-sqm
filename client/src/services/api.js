// client/src/services/api.js
import axios from 'axios';

// En desarrollo puedes definir en client/.env:
//   REACT_APP_API_URL=http://localhost:3000/api
// En producción lo dejamos en '' para que axios use el mismo origen + /api
const BASE = process.env.REACT_APP_API_URL || '/api';

const api = axios.create({
  baseURL: BASE,
  timeout: 10000,
});

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