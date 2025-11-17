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

/**
 * Obtiene datos de viento (VV y DV) específicos de la estación Hospital (E5)
 * con implementación de caché Redis para los últimos 2 días
 */
async function getHospitalWindData() {
    try {
        const key = 'measurements:hospital:wind';
        const now = Date.now();
        const twoDaysAgo = now - (2 * 24 * 60 * 60 * 1000); // 2 días de datos

        // Diagnóstico inicial
        console.log(`[Redis] Diagnóstico inicial para ${key}:`, {
            timestamp: new Date().toISOString(),
            station: 'E5 (Hospital)',
            variables: ['VV', 'DV']
        });

        // 1. Verificar si necesitamos actualizar los datos
        const latest = await redisClient.zrevrange(key, 0, 0, 'WITHSCORES');
        const lastTs = latest.length === 2 ? Number(latest[1]) : null;

        if (!lastTs || (now - lastTs) > 60000) { // Actualizar si no hay datos o han pasado más de 1 minuto
            console.log(`[Redis] Actualizando datos para ${key}`);
            const newData = await getHospitalWindDataFromDatabase();
            
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

        // 2. Obtener datos del rango solicitado (últimos 2 días)
        const cacheItems = await redisClient.zrevrangebyscore(key, now, twoDaysAgo);
        const result = cacheItems.map(item => JSON.parse(item));
        
        console.log(`[Redis] Datos obtenidos de caché:`, {
            count: result.length,
            firstTimestamp: result[0]?.timestamp,
            lastTimestamp: result[result.length - 1]?.timestamp
        });
        
        return result;

    } catch (error) {
        console.error(`[Redis] Error en getHospitalWindData:`, error);
        return await getHospitalWindDataFromDatabase();
    }
}

/**
 * Obtiene datos de viento de la estación Hospital desde la base de datos
 */
async function getHospitalWindDataFromDatabase() {
    try {
        const sql = `
            SELECT
                station_name,
                variable_name,
                timestamp,
                valor
            FROM
                datos
            WHERE
                station_name = 'E5'
                AND variable_name IN ('VV', 'DV')
                AND timestamp >= DATE_SUB(NOW(), INTERVAL 2 DAY)
            ORDER BY
                timestamp DESC, variable_name
        `;

        const [rows] = await readerPool.query(sql);
        console.log(`[Redis] Datos obtenidos de BD para Hospital:`, {
            count: rows.length,
            firstTimestamp: rows[0]?.timestamp,
            lastTimestamp: rows[rows.length - 1]?.timestamp
        });

        // Agrupar los datos por timestamp para combinar VV y DV
        const groupedData = {};

        rows.forEach(row => {
            const key = `${row.timestamp}`;
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

        const result = Object.values(groupedData)
            .filter(item => item.velocidad !== null || item.direccion !== null) // Solo registros con al menos un valor
            .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
        
        console.log('Resultado final agrupado para Hospital:', {
            count: result.length,
            firstTimestamp: result[0]?.timestamp,
            lastTimestamp: result[result.length - 1]?.timestamp
        });
        
        return result;
    } catch (error) {
        console.error('Error en getHospitalWindDataFromDatabase:', error);
        throw error;
    }
}

/**
 * Obtiene el promedio de 24 horas para la estación Hospital (E5)
 * Calcula promedios por hora de las últimas 24 horas y luego promedia esos 24 promedios
 */
async function getHospital24hAverage() {
    try {
        console.log('[Hospital 24h] Iniciando cálculo de promedio de 24 horas...');
        
        const sql = `
            WITH hourly_averages AS (
                SELECT 
                    DATE_FORMAT(timestamp, '%Y-%m-%d %H:00:00') as hour_start,
                    variable_name,
                    AVG(valor) as avg_value,
                    COUNT(*) as record_count
                FROM datos 
                WHERE 
                    station_name = 'E5'
                    AND variable_name IN ('PM10', 'VV', 'DV')
                    AND timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                    AND valor IS NOT NULL
                    AND valor > 0
                GROUP BY 
                    DATE_FORMAT(timestamp, '%Y-%m-%d %H:00:00'),
                    variable_name
            ),
            hourly_combined AS (
                SELECT 
                    hour_start,
                    MAX(CASE WHEN variable_name = 'PM10' THEN avg_value END) as pm10_avg,
                    MAX(CASE WHEN variable_name = 'VV' THEN avg_value END) as vv_avg,
                    MAX(CASE WHEN variable_name = 'DV' THEN avg_value END) as dv_avg,
                    COUNT(DISTINCT variable_name) as variables_count
                FROM hourly_averages
                GROUP BY hour_start
            ),
            final_averages AS (
                SELECT 
                    AVG(pm10_avg) as pm10_24h_avg,
                    AVG(vv_avg) as vv_24h_avg,
                    AVG(dv_avg) as dv_24h_avg,
                    COUNT(*) as hours_with_data,
                    MIN(hour_start) as first_hour,
                    MAX(hour_start) as last_hour
                FROM hourly_combined
                WHERE variables_count > 0
            )
            SELECT 
                pm10_24h_avg,
                vv_24h_avg,
                dv_24h_avg,
                hours_with_data,
                first_hour,
                last_hour,
                NOW() as calculation_time
            FROM final_averages
        `;

        const [rows] = await readerPool.query(sql);
        
        if (rows.length === 0) {
            console.log('[Hospital 24h] No se encontraron datos para calcular el promedio');
            return {
                success: false,
                message: 'No hay datos suficientes para calcular el promedio de 24 horas',
                data: null
            };
        }

        const result = rows[0];
        
        console.log('[Hospital 24h] Cálculo completado:', {
            pm10_24h_avg: result.pm10_24h_avg,
            vv_24h_avg: result.vv_24h_avg,
            dv_24h_avg: result.dv_24h_avg,
            hours_with_data: result.hours_with_data,
            first_hour: result.first_hour,
            last_hour: result.last_hour
        });

        return {
            success: true,
            data: {
                pm10: {
                    valor: result.pm10_24h_avg ? parseFloat(result.pm10_24h_avg).toFixed(4) : null,
                    horas_con_datos: result.hours_with_data,
                    primera_hora: result.first_hour,
                    ultima_hora: result.last_hour
                },
                viento: {
                    velocidad: result.vv_24h_avg ? parseFloat(result.vv_24h_avg).toFixed(4) : null,
                    direccion: result.dv_24h_avg ? parseFloat(result.dv_24h_avg).toFixed(4) : null,
                    horas_con_datos: result.hours_with_data,
                    primera_hora: result.first_hour,
                    ultima_hora: result.last_hour
                },
                calculado_en: result.calculation_time
            }
        };

    } catch (error) {
        console.error('[Hospital 24h] Error al calcular promedio de 24 horas:', error);
        return {
            success: false,
            message: 'Error al calcular el promedio de 24 horas',
            error: error.message,
            data: null
        };
    }
}

module.exports = {
    getMeasurementsByVariable,
    getWindData,
    getMultipleVariablesData,
    getPM10Data,
    getHospitalWindData,
    getHospital24hAverage
};