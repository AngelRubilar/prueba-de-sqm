const { readerPool } = require('../config/database');
const redisClient = require('../config/redis');

/**
 * Obtiene todas las mediciones de una variable en un rango opcional de fechas.
 */
async function getMeasurementsByVariable({ variable, from, to }) {
  const key = `measurements:${variable}`;
  // 1. Obtener el último timestamp almacenado en Redis
  const latest = await redisClient.zrevrange(key, 0, 0, 'WITHSCORES');
  const lastTs = latest.length === 2 ? Number(latest[1]) : null;
  // 2. Definir consulta a BD: si no hay datos en Redis, traer últimos 7 días; sino solo nuevos registros
  let sql, params;
  if (!lastTs) {
    const sinceDate = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
    sql = 'SELECT * FROM datos WHERE variable_name = ? AND timestamp >= ? ORDER BY timestamp DESC';
    params = [variable, sinceDate];
  } else {
    sql = 'SELECT * FROM datos WHERE variable_name = ? AND timestamp > ? ORDER BY timestamp DESC';
    params = [variable, new Date(lastTs)];
  }
  const [newRows] = await readerPool.query(sql, params);
  // 3. Almacenar nuevos registros en Redis (score = timestamp en ms)
  for (const row of newRows.reverse()) {
    const score = new Date(row.timestamp).getTime();
    await redisClient.zadd(key, score, JSON.stringify(row));
  }
  // 4. Eliminar de Redis datos con más de 7 días
  const cutoff = Date.now() - 7 * 24 * 60 * 60 * 1000;
  await redisClient.zremrangebyscore(key, 0, cutoff);
  // 5. Obtener del caché el rango solicitado y devolver resultado ordenado descendentemente
  const startScore = from ? new Date(from).getTime() : cutoff;
  const endScore = to ? new Date(to).getTime() : Date.now();
  const cacheItems = await redisClient.zrangebyscore(key, startScore, endScore);
  const results = cacheItems.map(item => JSON.parse(item));
  return results.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
}

/**
 * Obtiene datos de viento (dirección y velocidad) en un rango opcional de fechas.
 */
async function getWindData() {
  try {
    const sql = `
      WITH RankedData AS (
          SELECT
              station_name,
              variable_name,
              timestamp,
              valor,
              ROW_NUMBER() OVER(PARTITION BY station_name, variable_name ORDER BY timestamp DESC) as rn
          FROM
              datos
          WHERE
              variable_name IN ('VV', 'DV')
      )
      SELECT
          station_name,
          variable_name,
          timestamp,
          valor
      FROM
          RankedData
      WHERE
          rn <= 5
      ORDER BY
          station_name,
          timestamp DESC
    `;

    const [rows] = await readerPool.query(sql);
    console.log('Datos obtenidos de la base de datos:', rows); // Depuración

    // Agrupar los datos por estación y timestamp
    const groupedData = {};

    rows.forEach(row => {
      const key = `${row.timestamp}-${row.station_name}`;
      if (!groupedData[key]) {
        groupedData[key] = {
          timestamp: row.timestamp,
          station_name: row.station_name,
          velocidad: null,
          direccion: null,
        };
      }

      if (row.variable_name === 'VV') {
        groupedData[key].velocidad = parseFloat(row.valor);
      } else if (row.variable_name === 'DV') {
        groupedData[key].direccion = parseFloat(row.valor);
      }
    });

    const result = Object.values(groupedData).sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
    console.log('Resultado final agrupado:', result); // Depuración
    return result;
  } catch (error) {
    console.error('Error en getWindData:', error); // Log del error
    throw error; // Lanza el error para que el servicio lo maneje
  }
}

/**
 * Obtiene mediciones de múltiples variables en un rango opcional de fechas.
 * variables: array de strings con los nombres de variable.
 */
async function getMultipleVariablesData({ variables, from, to }) {
  // Si no hay variables, retornar vacío
  if (!Array.isArray(variables) || variables.length === 0) {
    return [];
  }
  // Usar la lógica de caché por variable
  const resultsArray = await Promise.all(
    variables.map(v => getMeasurementsByVariable({ variable: v, from, to }))
  );
  // Aplanar el arreglo y ordenar por timestamp descendente
  return resultsArray.flat().sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
}

/**
 * Alias para obtener datos de PM10 en un rango opcional de fechas.
 */
async function getPM10Data({ from, to }) {
  return getMeasurementsByVariable({ variable: 'PM10', from, to });
}

module.exports = {
  getMeasurementsByVariable,
  getWindData,
  getMultipleVariablesData,
  getPM10Data
}; 