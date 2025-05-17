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
    let { from, to, variables } = req.query;
    if (!variables) {
      return res.status(400).json({ error: 'Debe especificar variables como CSV en query param' });
    }
    const list = variables.split(',').map(v => v.trim());
    const data = await service.fetchVariables({ variables: list, from, to });
    res.json(data);
  } catch (e) {
    next(e);
  }
}

module.exports = {
  getPM10Data,
  getWindData,
  getSO2Data,
  getVariablesData
};