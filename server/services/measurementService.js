const repo = require('../repositories/measurementRepository');

/**
 * Obtiene datos de PM10 en un rango opcional de fechas.
 */
async function fetchPM10({ from, to }) {
  return repo.getPM10Data({ from, to });
}

/**
 * Obtiene datos de viento (velocidad y dirección) en un rango opcional.
 */
async function fetchWindData() {
  const windData = await repo.getWindData(); // Llama al repository
  return windData; // Devuelve los datos sin modificaciones
}

/**
 * Obtiene datos de SO2 en un rango opcional de fechas.
 */
async function fetchSO2({ from, to }) {
  return repo.getMeasurementsByVariable({ variable: 'SO2', from, to });
}

/**
 * Obtiene datos de múltiples variables en un rango opcional.
 * variables: array con nombres de variable.
 */
async function fetchVariables({ variables = ['HR', 'Temperatura', 'PM2_5'], from, to }) {
  return repo.getMultipleVariablesData({ variables, from, to });
}

/**
 * Obtiene datos de viento (velocidad y dirección) específicos de la estación Hospital (E5)
 * con implementación de caché Redis para los últimos 2 días
 */
async function fetchHospitalWindData() {
  return repo.getHospitalWindData();
}

module.exports = {
  fetchPM10,
  fetchWindData,
  fetchSO2,
  fetchVariables,
  fetchHospitalWindData,
};