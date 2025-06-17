// server/repositories/mqttRepository.js
const { writerPool } = require('../config/database');
const winston = require('winston');
const path = require('path');
const fs = require('fs');

class MqttRepository {
    constructor() {
        this.writerPool = writerPool;
        this.logger = this.setupLogger();
    }

    setupLogger() {
        // Asegurar que el directorio logs existe
        const logDir = path.join(__dirname, '..', 'logs');
        if (!fs.existsSync(logDir)) {
            fs.mkdirSync(logDir);
        }

        return winston.createLogger({
            level: 'debug',
            format: winston.format.combine(
                winston.format.timestamp(),
                winston.format.json()
            ),
            transports: [
                // Log en consola con colores
                new winston.transports.Console({
                    format: winston.format.combine(
                        winston.format.colorize(),
                        winston.format.simple()
                    )
                }),
                // Log de errores
                new winston.transports.File({ 
                    filename: path.join(logDir, 'mqtt-repository-error.log'), 
                    level: 'error',
                    maxsize: 5242880, // 5MB
                    maxFiles: 5
                }),
                // Log general
                new winston.transports.File({ 
                    filename: path.join(logDir, 'mqtt-repository-combined.log'),
                    maxsize: 5242880, // 5MB
                    maxFiles: 5
                })
            ]
        });
    }

    async verifyStation(station_name) {
        const connection = await this.writerPool.getConnection();
        try {
            this.logger.debug(`Verificando existencia de estación: ${station_name}`);
            const [rows] = await connection.query(
                'SELECT 1 FROM estaciones WHERE station_name = ?',
                [station_name]
            );
            const exists = rows.length > 0;
            this.logger.debug(`Estación ${station_name} ${exists ? 'existe' : 'no existe'}`);
            return exists;
        } catch (error) {
            this.logger.error('Error al verificar estación:', {
                error: error.message,
                station: station_name
            });
            throw error;
        } finally {
            connection.release();
        }
    }

    async checkDuplicateData(data) {
        const connection = await this.writerPool.getConnection();
        try {
            this.logger.debug('Verificando duplicado:', {
                station: data.station_name,
                variable: data.variable_name,
                timestamp: data.timestamp
            });

            const [rows] = await connection.query(
                'SELECT 1 FROM datos WHERE timestamp = ? AND station_name = ? AND variable_name = ?',
                [data.timestamp, data.station_name, data.variable_name]
            );
            
            const isDuplicate = rows.length > 0;
            if (isDuplicate) {
                this.logger.info('Dato duplicado encontrado:', {
                    station: data.station_name,
                    variable: data.variable_name,
                    timestamp: data.timestamp
                });
            }
            
            return isDuplicate;
        } catch (error) {
            this.logger.error('Error al verificar duplicado:', {
                error: error.message,
                data: data
            });
            throw error;
        } finally {
            connection.release();
        }
    }

    async saveStationData(data) {
        const connection = await this.writerPool.getConnection();
        try {
            this.logger.debug('Guardando datos de estación:', {
                station: data.station_name,
                variable: data.variable_name,
                timestamp: data.timestamp
            });

            const [result] = await connection.query(
                'INSERT INTO datos (timestamp, station_name, variable_name, valor) VALUES (?, ?, ?, ?)',
                [data.timestamp, data.station_name, data.variable_name, data.valor]
            );

            this.logger.info('Datos guardados exitosamente:', {
                insertId: result.insertId,
                station: data.station_name,
                variable: data.variable_name
            });

            return result.insertId;
        } catch (error) {
            this.logger.error('Error al guardar datos:', {
                error: error.message,
                data: data
            });
            throw error;
        } finally {
            connection.release();
        }
    }

    async getStationStats(station_name, startDate, endDate) {
        const connection = await this.writerPool.getConnection();
        try {
            this.logger.debug('Obteniendo estadísticas de estación:', {
                station: station_name,
                startDate,
                endDate
            });

            const [rows] = await connection.query(
                `SELECT 
                    variable_name,
                    COUNT(*) as total_records,
                    MIN(valor) as min_value,
                    MAX(valor) as max_value,
                    AVG(valor) as avg_value,
                    STD(valor) as std_value
                FROM datos 
                WHERE station_name = ? 
                AND timestamp BETWEEN ? AND ?
                GROUP BY variable_name`,
                [station_name, startDate, endDate]
            );

            this.logger.debug('Estadísticas obtenidas:', {
                station: station_name,
                variables: rows.map(r => r.variable_name)
            });

            return rows;
        } catch (error) {
            this.logger.error('Error al obtener estadísticas:', {
                error: error.message,
                station: station_name,
                startDate,
                endDate
            });
            throw error;
        } finally {
            connection.release();
        }
    }

    async getHistoricalData(station_name, variable, startDate, endDate) {
        const connection = await this.writerPool.getConnection();
        try {
            this.logger.debug('Obteniendo datos históricos:', {
                station: station_name,
                variable,
                startDate,
                endDate
            });

            const [rows] = await connection.query(
                `SELECT timestamp, valor 
                FROM datos 
                WHERE station_name = ? 
                AND variable_name = ? 
                AND timestamp BETWEEN ? AND ?
                ORDER BY timestamp`,
                [station_name, variable, startDate, endDate]
            );

            this.logger.debug('Datos históricos obtenidos:', {
                station: station_name,
                variable,
                count: rows.length
            });

            return rows;
        } catch (error) {
            this.logger.error('Error al obtener datos históricos:', {
                error: error.message,
                station: station_name,
                variable,
                startDate,
                endDate
            });
            throw error;
        } finally {
            connection.release();
        }
    }

    async getLatestValue(station_name, variable) {
        const connection = await this.writerPool.getConnection();
        try {
            this.logger.debug('Obteniendo último valor:', {
                station: station_name,
                variable
            });

            const [rows] = await connection.query(
                `SELECT timestamp, valor 
                FROM datos 
                WHERE station_name = ? 
                AND variable_name = ? 
                ORDER BY timestamp DESC 
                LIMIT 1`,
                [station_name, variable]
            );

            const result = rows[0] || null;
            
            if (result) {
                this.logger.debug('Último valor encontrado:', {
                    station: station_name,
                    variable,
                    timestamp: result.timestamp,
                    valor: result.valor
                });
            } else {
                this.logger.debug('No se encontraron valores:', {
                    station: station_name,
                    variable
                });
            }

            return result;
        } catch (error) {
            this.logger.error('Error al obtener último valor:', {
                error: error.message,
                station: station_name,
                variable
            });
            throw error;
        } finally {
            connection.release();
        }
    }

    // Método para obtener el rango de fechas disponible para una estación
    async getDateRange(station_name) {
        const connection = await this.writerPool.getConnection();
        try {
            this.logger.debug('Obteniendo rango de fechas:', {
                station: station_name
            });

            const [rows] = await connection.query(
                `SELECT 
                    MIN(timestamp) as first_date,
                    MAX(timestamp) as last_date
                FROM datos 
                WHERE station_name = ?`,
                [station_name]
            );

            const result = rows[0] || null;
            
            if (result) {
                this.logger.debug('Rango de fechas encontrado:', {
                    station: station_name,
                    first_date: result.first_date,
                    last_date: result.last_date
                });
            } else {
                this.logger.debug('No se encontraron fechas:', {
                    station: station_name
                });
            }

            return result;
        } catch (error) {
            this.logger.error('Error al obtener rango de fechas:', {
                error: error.message,
                station: station_name
            });
            throw error;
        } finally {
            connection.release();
        }
    }

    // Método para obtener las variables disponibles de una estación
    async getAvailableVariables(station_name) {
        const connection = await this.writerPool.getConnection();
        try {
            this.logger.debug('Obteniendo variables disponibles:', {
                station: station_name
            });

            const [rows] = await connection.query(
                `SELECT DISTINCT variable_name 
                FROM datos 
                WHERE station_name = ?
                ORDER BY variable_name`,
                [station_name]
            );

            const variables = rows.map(row => row.variable_name);
            
            this.logger.debug('Variables encontradas:', {
                station: station_name,
                count: variables.length,
                variables
            });

            return variables;
        } catch (error) {
            this.logger.error('Error al obtener variables disponibles:', {
                error: error.message,
                station: station_name
            });
            throw error;
        } finally {
            connection.release();
        }
    }
}

module.exports = MqttRepository;