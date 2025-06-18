const { readerPool } = require('../config/database');
const { redisClient, checkRedisConnection } = require('../config/redis');

async function getMeasurementsByVariable({ variable, from, to }) {
    try {
        const key = `measurements:${variable}`;
        const now = Date.now();
        const fourDaysAgo = now - (4 * 24 * 60 * 60 * 1000); // 4 días de datos

        // Diagnóstico inicial
        console.log(`[Redis] Diagnóstico inicial para ${key}:`, {
            timestamp: new Date().toISOString(),
            from: from ? new Date(from).toISOString() : 'undefined',
            to: to ? new Date(to).toISOString() : 'undefined'
        });

        // 1. Verificar si necesitamos actualizar los datos
        const latest = await redisClient.zrevrange(key, 0, 0, 'WITHSCORES');
        const lastTs = latest.length === 2 ? Number(latest[1]) : null;

        if (!lastTs || (now - lastTs) > 60000) {
           // console.log(`[Redis] Actualizando datos para ${variable}`);
            const newData = await getDataFromDatabase(variable, from, to);
            
            if (newData.length > 0) {
                const pipeline = redisClient.pipeline();
                
                // Almacenar nuevos datos
                for (const row of newData) {
                    const score = new Date(row.timestamp).getTime();
                    pipeline.zadd(key, score, JSON.stringify(row));
                }
                
                // Mantener datos de los últimos 4 días
                pipeline.zremrangebyscore(key, 0, fourDaysAgo);
                
                // TTL de 24 horas
                pipeline.expire(key, 24 * 3600);
                
                await pipeline.exec();
                
                // Verificar almacenamiento
                const storedCount = await redisClient.zcard(key);
                console.log(`[Redis] Datos almacenados en Redis: ${storedCount} registros`);
            }
        }

        // 2. Obtener datos del rango solicitado
        let startScore, endScore;
        
        if (from && to) {
            startScore = new Date(from).getTime();
            endScore = new Date(to).getTime();
        } else {
            endScore = now;
            startScore = fourDaysAgo;
        }

        const cacheItems = await redisClient.zrevrangebyscore(key, endScore, startScore);
        return cacheItems.map(item => JSON.parse(item));

    } catch (error) {
        console.error(`[Redis] Error en getMeasurementsByVariable para ${variable}:`, error);
        return await getDataFromDatabase(variable, from, to);
    }
}

async function getDataFromDatabase(variable, from, to) {
    let sql, params;
    if (from && to) {
        sql = 'SELECT * FROM datos WHERE variable_name = ? AND timestamp BETWEEN ? AND ? ORDER BY timestamp DESC';
        params = [variable, new Date(from), new Date(to)];
    } else {
        const fourDaysAgo = new Date(Date.now() - 4 * 24 * 60 * 60 * 1000);
        sql = 'SELECT * FROM datos WHERE variable_name = ? AND timestamp >= ? ORDER BY timestamp DESC';
        params = [variable, fourDaysAgo];
    }
    
    const [rows] = await readerPool.query(sql, params);
    
    if (rows.length > 0) {
        console.log(`[Redis] Datos obtenidos de BD:`, {
            count: rows.length,
            firstTimestamp: new Date(rows[0].timestamp).toISOString(),
            lastTimestamp: new Date(rows[rows.length - 1].timestamp).toISOString()
        });
    }
    
    return rows;
}

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
        //console.log('Datos obtenidos de la base de datos:', rows);

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
        console.log('Resultado final agrupado:', result);
        return result;
    } catch (error) {
        console.error('Error en getWindData:', error);
        throw error;
    }
}

// server/repositories/measurementRepository.js

async function getMultipleVariablesData({ variables = ['HR', 'Temperatura', 'PM2_5'], from, to }) {
    if (!Array.isArray(variables) || variables.length === 0) {
        return [];
    }
    
    const stations = ['E1', 'E2', 'E5', 'E6', 'E7', 'E8'];
    
    try {
        // Crear una clave única para Redis basada en las variables y estaciones
        const key = `measurements:multiple:${variables.sort().join('-')}:${stations.sort().join('-')}`;
        const now = Date.now();
        const twoDaysAgo = now - (2 * 24 * 60 * 60 * 1000); // 2 días de datos

        // Diagnóstico inicial
        console.log(`[Redis] Diagnóstico inicial para ${key}:`, {
            timestamp: new Date().toISOString(),
            from: from ? new Date(from).toISOString() : 'undefined',
            to: to ? new Date(to).toISOString() : 'undefined'
        });

        // 1. Verificar si necesitamos actualizar los datos
        const latest = await redisClient.zrevrange(key, 0, 0, 'WITHSCORES');
        const lastTs = latest.length === 2 ? Number(latest[1]) : null;

        if (!lastTs || (now - lastTs) > 60000) { // Actualizar si no hay datos o han pasado más de 1 minuto
            console.log(`[Redis] Actualizando datos para ${key}`);
            const newData = await getMultipleVariablesFromDatabase(variables, stations, from, to);
            
            if (newData.length > 0) {
                const pipeline = redisClient.pipeline();
                
                // Almacenar nuevos datos
                for (const row of newData) {
                    const score = new Date(row.timestamp).getTime();
                    pipeline.zadd(key, score, JSON.stringify(row));
                }
                
                // Mantener datos de los últimos 2 días
                pipeline.zremrangebyscore(key, 0, twoDaysAgo);
                
                // TTL de 24 horas
                pipeline.expire(key, 24 * 3600);
                
                await pipeline.exec();
                
                // Verificar almacenamiento
                const storedCount = await redisClient.zcard(key);
                console.log(`[Redis] Datos almacenados en Redis: ${storedCount} registros`);
            }
        }

        // 2. Obtener datos del rango solicitado
        let startScore, endScore;
        
        if (from && to) {
            startScore = new Date(from).getTime();
            endScore = new Date(to).getTime();
        } else {
            endScore = now;
            startScore = twoDaysAgo;
        }

        const cacheItems = await redisClient.zrevrangebyscore(key, endScore, startScore);
        return cacheItems.map(item => JSON.parse(item));

    } catch (error) {
        console.error(`[Redis] Error en getMultipleVariablesData:`, error);
        return await getMultipleVariablesFromDatabase(variables, stations, from, to);
    }
}

// Función auxiliar para obtener datos de la base de datos
async function getMultipleVariablesFromDatabase(variables, stations, from, to) {
    try {
        let sql = `
            WITH RankedData AS (
                SELECT 
                    *,
                    ROW_NUMBER() OVER (
                        PARTITION BY station_name, variable_name 
                        ORDER BY timestamp DESC
                    ) as rn
                FROM datos
                WHERE variable_name IN (?)
                AND station_name IN (?)
                AND timestamp >= DATE_SUB(NOW(), INTERVAL 2 DAY)
            )
            SELECT 
                timestamp,
                station_name,
                variable_name,
                valor
            FROM RankedData
            ORDER BY timestamp DESC, station_name, variable_name
        `;
        
        const params = [variables, stations];
        
        const [rows] = await readerPool.query(sql, params);
        
        // Agrupar los datos por estación y variable
        const groupedData = {};
        
        rows.forEach(row => {
            const key = `${row.station_name}-${row.variable_name}`;
            if (!groupedData[key]) {
                groupedData[key] = [];
            }
            groupedData[key].push({
                timestamp: row.timestamp,
                station_name: row.station_name,
                variable_name: row.variable_name,
                valor: parseFloat(row.valor)
            });
        });
        
        // Ordenar los datos por timestamp más reciente
        const result = Object.values(groupedData).flat().sort((a, b) => 
            new Date(b.timestamp) - new Date(a.timestamp)
        );
        
        console.log(`[Redis] Datos obtenidos de BD:`, {
            count: result.length,
            firstTimestamp: result[0]?.timestamp,
            lastTimestamp: result[result.length - 1]?.timestamp
        });
        
        return result;
        
    } catch (error) {
        console.error('Error en getMultipleVariablesFromDatabase:', error);
        throw error;
    }
}

async function getPM10Data({ from, to }) {
    return getMeasurementsByVariable({ variable: 'PM10', from, to });
}

module.exports = {
    getMeasurementsByVariable,
    getWindData,
    getMultipleVariablesData,
    getPM10Data
};