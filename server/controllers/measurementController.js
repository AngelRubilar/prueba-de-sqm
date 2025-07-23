const service = require('../services/measurementService');

async function getPM10Data(req, res, next) {
  try {
    const { from, to } = req.query;
    const data = await service.fetchPM10({ from, to });
    res.json(data);
  } catch (e) {
    next(e);
  }
}

async function getWindData(req, res) {
  try {
    const data = await service.fetchWindData(); // Llama al servicio
    res.json(data); // Envía los datos al cliente
  } catch (error) {
    console.error('Error al obtener datos de viento:', error);
    res.status(500).json({ error: 'Error al obtener datos de viento' });
  }
}

async function getSO2Data(req, res, next) {
  try {
    const { from, to } = req.query;
    const data = await service.fetchSO2({ from, to });
    res.json(data);
  } catch (e) {
    next(e);
  }
}

async function getVariablesData(req, res, next) {
  try {
    const { from, to } = req.query;
    // Llamar al servicio con las variables por defecto
    const data = await service.fetchVariables({ 
      variables: ['HR', 'Temperatura', 'PM2_5'],
      from, 
      to 
    });
    res.json(data);
  } catch (e) {
    next(e);
  }
}

async function getHospitalWindData(req, res, next) {
  try {
    const data = await service.fetchHospitalWindData();
    res.json(data);
  } catch (e) {
    next(e);
  }
}

module.exports = {
  getPM10Data,
  getWindData,
  getSO2Data,
  getVariablesData,
  getHospitalWindData
};