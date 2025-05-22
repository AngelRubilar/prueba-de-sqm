const { readerPool } = require('../config/database');
const { redisClient, checkRedisConnection } = require('../config/redis');

/**
 * Obtiene todas las mediciones de una variable en un rango opcional de fechas.
 */
/* async function getMeasurementsByVariable({ variable, from, to }) {
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
} */
  async function getMeasurementsByVariable({ variable, from, to }) {
    try {
      // Verificar conexión a Redis
      const isRedisWorking = await checkRedisConnection();
      if (!isRedisWorking) {
        console.log('[Redis] No está respondiendo, usando base de datos directamente');
        return await getDataFromDatabase(variable, from, to);
      }
  
      const key = `measurements:${variable}`;
      const sevenDaysAgo = Date.now() - 7 * 24 * 60 * 60 * 1000;
  
      // 1. Obtener último timestamp de Redis
      const latest = await redisClient.zrevrange(key, 0, 0, 'WITHSCORES');
      const lastTs = latest.length === 2 ? Number(latest[1]) : null;
  
      console.log(`[Redis] Último timestamp para ${variable}:`, lastTs ? new Date(lastTs) : 'No hay datos');
  
      // 2. Obtener datos de la base de datos si es necesario
      let newRows = [];
      if (!lastTs || (from && new Date(from).getTime() < lastTs)) {
        console.log(`[Redis] Obteniendo datos de BD para ${variable}`);
        newRows = await getDataFromDatabase(variable, from, to);
        
        // 3. Almacenar en Redis
        if (newRows.length > 0) {
          console.log(`[Redis] Almacenando ${newRows.length} registros para ${variable}`);
          
          // Crear un pipeline para operaciones múltiples
          const pipeline = redisClient.pipeline();
          
          // Preparar los datos para Redis
          for (const row of newRows) {
            const score = new Date(row.timestamp).getTime();
            const member = JSON.stringify(row);
            pipeline.zadd(key, score, member);
          }
          
          // Ejecutar todas las operaciones
          await pipeline.exec();
          console.log(`[Redis] Datos almacenados exitosamente para ${variable}`);
        }
      }
  
      // 4. Limpiar datos antiguos de Redis
      await redisClient.zremrangebyscore(key, 0, sevenDaysAgo);
      console.log(`[Redis] Limpieza de datos antiguos completada para ${variable}`);
  
      // 5. Obtener datos del rango solicitado
      const startScore = from ? new Date(from).getTime() : sevenDaysAgo;
      const endScore = to ? new Date(to).getTime() : Date.now();
  
      console.log(`[Redis] Obteniendo datos para ${variable} desde ${new Date(startScore)} hasta ${new Date(endScore)}`);
      
      const cacheItems = await redisClient.zrevrangebyscore(key, endScore, startScore);
      console.log(`[Redis] Obtenidos ${cacheItems.length} registros de Redis para ${variable}`);
  
      // Verificar si obtuvimos datos de Redis
      if (cacheItems.length === 0) {
        console.log(`[Redis] No hay datos en caché para ${variable}, obteniendo de BD`);
        return await getDataFromDatabase(variable, from, to);
      }
  
      return cacheItems.map(item => JSON.parse(item));
    } catch (error) {
      console.error(`[Redis] Error en getMeasurementsByVariable para ${variable}:`, error);
      // Si hay error con Redis, fallback a base de datos
      return await getDataFromDatabase(variable, from, to);
    }
  }
  
  // Función auxiliar para obtener datos directamente de la base de datos
  async function getDataFromDatabase(variable, from, to) {
    let sql, params;
    if (from && to) {
      sql = 'SELECT * FROM datos WHERE variable_name = ? AND timestamp BETWEEN ? AND ? ORDER BY timestamp DESC';
      params = [variable, new Date(from), new Date(to)];
    } else {
      const sevenDaysAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
      sql = 'SELECT * FROM datos WHERE variable_name = ? AND timestamp >= ? ORDER BY timestamp DESC';
      params = [variable, sevenDaysAgo];
    }
    
    const [rows] = await readerPool.query(sql, params);
    return rows;
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