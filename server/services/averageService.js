const { writerPool, readerPool } = require('../config/database');
const moment = require('moment-timezone');

class AverageService {
    constructor() {
        this.timezone = 'America/Santiago';
        this.logger = console; // Puedes cambiar por Winston si prefieres
        
        // Verificar que los pools de conexión estén disponibles
        if (!readerPool) {
            throw new Error('Reader pool no está inicializado');
        }
        if (!writerPool) {
            throw new Error('Writer pool no está inicializado');
        }
        
        this.readerPool = readerPool;
        this.writerPool = writerPool;
        
        this.logger.info('✅ AverageService inicializado correctamente');
    }

    /**
     * Calcula promedios por hora y diarios para todas las estaciones configuradas
     * Se ejecuta cada hora para mantener datos actualizados
     */
    async calcularPromediosDiarios() {
        const connection = await writerPool.getConnection();
        const now = moment().tz(this.timezone);
        const today = now.format('YYYY-MM-DD');
        const currentHour = now.format('HH:mm:ss');
        
        try {
            this.logger.info(`Iniciando cálculo de promedios para ${today} a las ${currentHour}`);
            
            // Obtener configuración de variables por estación
            const configuracion = await this.obtenerConfiguracionVariables();
            
            // Calcular promedios para cada estación y variable
            for (const config of configuracion) {
                await this.calcularPromedioEstacionVariable(
                    connection, 
                    config.station_name, 
                    config.variable_name, 
                    today,
                    currentHour
                );
            }
            
            this.logger.info('Cálculo de promedios completado exitosamente');
            
        } catch (error) {
            this.logger.error('Error al calcular promedios:', error);
            throw error;
        } finally {
            connection.release();
        }
    }

    /**
     * Calcula promedios por hora y diario para una estación y variable específica
     */
    async calcularPromedioEstacionVariable(connection, stationName, variableName, fecha, hora) {
        try {
            // Calcular promedio por hora (última hora)
            const promedioHora = await this.calcularPromedioHora(stationName, variableName);
            
            // Calcular promedio diario (promedio de las 24 horas del día actual)
            const promedioDiario = await this.calcularPromedioDia(stationName, variableName, fecha);
            
            // Insertar nuevo registro en la tabla de promedios
            await this.insertarPromedioEnBD(
                connection, 
                stationName, 
                variableName, 
                fecha,
                hora,
                promedioHora, 
                promedioDiario
            );
            
            this.logger.debug(`Promedios calculados para ${stationName} - ${variableName}: Hora=${promedioHora}, Diario=${promedioDiario} a las ${hora}`);
            
        } catch (error) {
            this.logger.error(`Error calculando promedios para ${stationName} - ${variableName}:`, error);
            throw error;
        }
    }

    /**
     * Calcula el promedio de la última hora para una estación y variable
     */
    async calcularPromedioHora(stationName, variableName) {
        const connection = await readerPool.getConnection();
        try {
            // Usar la zona horaria de Chile para el cálculo
            const now = moment().tz(this.timezone);
            const oneHourAgo = now.clone().subtract(1, 'hour').format('YYYY-MM-DD HH:mm:ss');
            const currentTime = now.format('YYYY-MM-DD HH:mm:ss');
            
            const query = `
                SELECT 
                    AVG(valor) as promedio,
                    COUNT(*) as cantidad
                FROM datos 
                WHERE station_name = ? 
                AND variable_name = ? 
                AND timestamp >= ?
                AND timestamp <= ?
                AND valor IS NOT NULL
            `;
            
            const [rows] = await connection.query(query, [stationName, variableName, oneHourAgo, currentTime]);
            const promedio = rows[0]?.promedio;
            
            // Convertir a número para evitar problemas de tipo
            const promedioNumerico = parseFloat(promedio);
            
            // Debug: Log para ver qué está devolviendo
            this.logger.debug(`DEBUG ${stationName} ${variableName}: promedio=${promedio}, promedioNumerico=${promedioNumerico}, tipo=${typeof promedioNumerico}, isNaN=${isNaN(promedioNumerico)}, isFinite=${isFinite(promedioNumerico)}`);
            
            // Validar que el resultado sea un número válido
            return isNaN(promedioNumerico) || !isFinite(promedioNumerico) ? null : promedioNumerico;
            
        } finally {
            connection.release();
        }
    }

    /**
     * Calcula el promedio diario basado en las horas del día actual
     * Si no hay datos en una hora, se cuenta como 0
     */
    async calcularPromedioDia(stationName, variableName, fecha) {
        const connection = await readerPool.getConnection();
        try {
            // Usar la zona horaria de Chile para el cálculo
            const startOfDay = moment.tz(fecha, this.timezone).startOf('day').format('YYYY-MM-DD HH:mm:ss');
            const endOfDay = moment.tz(fecha, this.timezone).endOf('day').format('YYYY-MM-DD HH:mm:ss');
            
            // Obtener el promedio por hora para cada hora del día actual
            const query = `
                SELECT 
                    HOUR(timestamp) as hora,
                    AVG(valor) as promedio_hora,
                    COUNT(*) as cantidad_registros
                FROM datos 
                WHERE station_name = ? 
                AND variable_name = ? 
                AND timestamp >= ?
                AND timestamp <= ?
                AND valor IS NOT NULL
                GROUP BY HOUR(timestamp)
                ORDER BY hora
            `;
            
            const [rows] = await connection.query(query, [stationName, variableName, startOfDay, endOfDay]);
            
            // Crear un array de 24 horas, inicializando con 0
            const horasDelDia = new Array(24).fill(0);
            let horasConDatos = 0;
            
            // Llenar las horas que tienen datos
            rows.forEach(row => {
                // Convertir a número para evitar concatenación de strings
                horasDelDia[row.hora] = parseFloat(row.promedio_hora) || 0;
                horasConDatos++;
            });
            
            // Calcular el promedio de las 24 horas
            const sumaTotal = horasDelDia.reduce((sum, valor) => sum + valor, 0);
            const promedioDiario = sumaTotal / 24; // Siempre dividir por 24
            
            // Debug: Log para ver qué está calculando
            this.logger.debug(`DEBUG ${stationName} ${variableName}: sumaTotal=${sumaTotal}, promedioDiario=${promedioDiario}, tipo=${typeof promedioDiario}, isNaN=${isNaN(promedioDiario)}, isFinite=${isFinite(promedioDiario)}`);
            
            // Validar que el resultado sea un número válido
            const resultadoFinal = isNaN(promedioDiario) || !isFinite(promedioDiario) ? null : promedioDiario;
            
            this.logger.debug(`Promedio diario para ${stationName} ${variableName}: ${horasConDatos} horas con datos, promedio: ${resultadoFinal}`);
            
            return resultadoFinal;
            
        } finally {
            connection.release();
        }
    }

    /**
     * Inserta un nuevo registro de promedio en la base de datos
     */
    async insertarPromedioEnBD(connection, stationName, variableName, fecha, hora, promedioHora, promedioDia) {
        const query = `
            INSERT INTO promedios_diarios 
                (fecha, hora_calculo, station_name, variable_name, promedio_hora, promedio_dia, cantidad_registros)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        `;
        
        // Validar que los valores sean números válidos antes de insertar
        const promedioHoraValido = (promedioHora !== null && !isNaN(promedioHora) && isFinite(promedioHora)) ? promedioHora : null;
        const promedioDiaValido = (promedioDia !== null && !isNaN(promedioDia) && isFinite(promedioDia)) ? promedioDia : null;
        
        await connection.query(query, [
            fecha,
            hora,
            stationName, 
            variableName, 
            promedioHoraValido, 
            promedioDiaValido,
            promedioDiaValido ? 1 : 0 // Cantidad de registros (simplificado)
        ]);
    }

    /**
     * Obtiene la configuración de variables por estación
     */
    async obtenerConfiguracionVariables() {
        const connection = await readerPool.getConnection();
        try {
            // Intentar obtener configuración desde la tabla específica de configuración
            const query = `
                SELECT station_name, variable_name
                FROM configuracion_variables_estacion
                WHERE activo = TRUE
                ORDER BY station_name, variable_name
            `;
            
            const [rows] = await connection.query(query);
            
            // Si no hay configuración, usar configuración por defecto
            if (rows.length === 0) {
                this.logger.warn('No se encontró configuración en la tabla configuracion_variables_estacion, usando configuración por defecto');
                return this.getConfiguracionPorDefecto();
            }
            
            return rows;
            
        } catch (error) {
            // Si la tabla no existe, usar configuración por defecto
            if (error.code === 'ER_NO_SUCH_TABLE') {
                this.logger.warn('Tabla configuracion_variables_estacion no existe, usando configuración por defecto');
                return this.getConfiguracionPorDefecto();
            }
            throw error;
        } finally {
            connection.release();
        }
    }

    /**
     * Retorna la configuración por defecto de variables por estación
     */
    getConfiguracionPorDefecto() {
        return [
            { station_name: 'E1', variable_name: 'SO2' },
            { station_name: 'E1', variable_name: 'PM2_5' },
            { station_name: 'E1', variable_name: 'PM10' },
            { station_name: 'E2', variable_name: 'SO2' },
            { station_name: 'E2', variable_name: 'PM2_5' },
            { station_name: 'E4', variable_name: 'SO2' },
            { station_name: 'E4', variable_name: 'PM2_5' },
            { station_name: 'E5', variable_name: 'PM10' },
            { station_name: 'E6', variable_name: 'PM10' },
            { station_name: 'E6', variable_name: 'SO2' },
            { station_name: 'E7', variable_name: 'PM10' },
            { station_name: 'E7', variable_name: 'SO2' },
            { station_name: 'E7', variable_name: 'PM2_5' },
            { station_name: 'E8', variable_name: 'PM10' },
            { station_name: 'E8', variable_name: 'SO2' },
            { station_name: 'E8', variable_name: 'PM2_5' },
            { station_name: 'E9', variable_name: 'PM10' },
            { station_name: 'E9', variable_name: 'SO2' },
            { station_name: 'E9', variable_name: 'PM2_5' },
            { station_name: 'E10', variable_name: 'PM10' },
            { station_name: 'E10', variable_name: 'PM2_5' }
        ];
    }

    /**
     * Obtiene el último promedio disponible para una estación y variable
     */
    async obtenerUltimoPromedio(stationName, variableName) {
        const connection = await readerPool.getConnection();
        try {
            const query = `
                SELECT 
                    fecha,
                    hora_calculo,
                    station_name,
                    variable_name,
                    promedio_hora,
                    promedio_dia,
                    fecha_creacion
                FROM promedios_diarios 
                WHERE station_name = ? 
                AND variable_name = ? 
                ORDER BY fecha DESC, hora_calculo DESC, fecha_creacion DESC 
                LIMIT 1
            `;
            
            const [rows] = await connection.query(query, [stationName, variableName]);
            return rows[0] || null;
            
        } finally {
            connection.release();
        }
    }

    /**
     * Obtiene promedios para múltiples estaciones y variables
     */
    async obtenerPromediosMultiples(configuracion) {
        const connection = await readerPool.getConnection();
        try {
            const placeholders = configuracion.map(() => '(?, ?)').join(',');
            const params = configuracion.flatMap(config => [config.station_name, config.variable_name]);
            
            const query = `
                SELECT 
                    pd.fecha,
                    pd.hora_calculo,
                    pd.station_name,
                    pd.variable_name,
                    pd.promedio_hora,
                    pd.promedio_dia,
                    pd.cantidad_registros,
                    pd.fecha_creacion
                FROM promedios_diarios pd
                INNER JOIN (
                    SELECT station_name, variable_name, MAX(id) as max_id
                    FROM promedios_diarios
                    WHERE (station_name, variable_name) IN (${placeholders})
                    GROUP BY station_name, variable_name
                ) latest ON pd.id = latest.max_id
                ORDER BY pd.station_name, pd.variable_name
            `;
            
            const [rows] = await connection.query(query, params);
            return rows;
            
        } finally {
            connection.release();
        }
    }

    /**
     * Obtiene promedios históricos para un rango de fechas
     */
    async obtenerPromediosHistoricos(stationName, variableName, fechaInicio, fechaFin) {
        const connection = await readerPool.getConnection();
        try {
            const query = `
                SELECT 
                    fecha,
                    hora_calculo,
                    station_name,
                    variable_name,
                    promedio_hora,
                    promedio_dia,
                    cantidad_registros,
                    fecha_creacion
                FROM promedios_diarios 
                WHERE station_name = ? 
                AND variable_name = ? 
                AND fecha BETWEEN ? AND ?
                ORDER BY fecha ASC, hora_calculo ASC
            `;
            
            const [rows] = await connection.query(query, [stationName, variableName, fechaInicio, fechaFin]);
            return rows;
            
        } finally {
            connection.release();
        }
    }

    /**
     * Limpia promedios antiguos (más de 30 días)
     */
    async limpiarPromediosAntiguos() {
        const connection = await writerPool.getConnection();
        try {
            const fechaLimite = moment().tz(this.timezone).subtract(30, 'days').format('YYYY-MM-DD');
            
            const query = `
                DELETE FROM promedios_diarios 
                WHERE fecha < ?
            `;
            
            const [result] = await connection.query(query, [fechaLimite]);
            this.logger.info(`Limpiados ${result.affectedRows} registros antiguos de promedios`);
            
        } catch (error) {
            this.logger.error('Error limpiando promedios antiguos:', error);
            throw error;
        } finally {
            connection.release();
        }
    }
}

module.exports = AverageService; 