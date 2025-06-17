// server/services/mqttService.js
const mqtt = require('mqtt');
const winston = require('winston');
const mqttConfig = require('../config/mqtt.config');
const MqttRepository = require('../repositories/mqttRepository');
const path = require('path');
const fs = require('fs');

//verficar estaciones
const estacionesValidas = Object.values(require('../config/nombreEstaciones'));

class MqttService {
    constructor() {
        this.mqttUrl = `mqtt://${mqttConfig.server.host}:${mqttConfig.server.port}`;
        this.mqttRepository = new MqttRepository();
        this.logger = this.setupLogger();
        this.client = null;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        
        // Iniciar conexión MQTT
        this.initializeMqtt();
        
        // Log inicial
        this.logger.info('MqttService inicializado', {
            brokerUrl: this.mqttUrl,
            topics: mqttConfig.topics
        });
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
                    filename: path.join(logDir, 'mqtt-error.log'), 
                    level: 'error',
                    maxsize: 5242880, // 5MB
                    maxFiles: 5
                }),
                // Log general
                new winston.transports.File({ 
                    filename: path.join(logDir, 'mqtt-combined.log'),
                    maxsize: 5242880, // 5MB
                    maxFiles: 5
                })
            ]
        });
    }

    initializeMqtt() {
        this.logger.info(`Conectando a MQTT broker: ${this.mqttUrl}`);
        
        // Configuración del cliente MQTT
        const options = {
            clientId: `sqm-api-${Math.random().toString(16).slice(3)}`,
            clean: true,
            reconnectPeriod: 5000,
            connectTimeout: 30 * 1000,
            keepalive: 60,
            username: process.env.MQTT_USERNAME,
            password: process.env.MQTT_PASSWORD
        };

        this.logger.debug('Opciones de conexión MQTT:', options);
        
        this.client = mqtt.connect(this.mqttUrl, options);
        this.setupEventHandlers();
    }

    setupEventHandlers() {
        // Evento de conexión
        this.client.on('connect', () => {
            this.isConnected = true;
            this.reconnectAttempts = 0;
            this.logger.info('Conectado al broker MQTT');
            
            // Suscribirse al tópico específico
            const topic = mqttConfig.topics.STATION_DATA;
            this.logger.info(`Intentando suscribirse al tópico: ${topic}`);
            
            this.client.subscribe(topic, (err) => {
                if (err) {
                    this.logger.error(`Error al suscribirse a ${topic}:`, err);
                } else {
                    this.logger.info(`Suscrito exitosamente a ${topic}`);
                }
            });
        });

        // Evento de mensaje recibido
        this.client.on('message', async (topic, message) => {
            console.log('Mensaje recibido en MQTT:', topic, message.toString());
            try {
                // Log del mensaje recibido
                this.logger.info('Mensaje MQTT recibido:', {
                    topic,
                    messageLength: message.length,
                    messageContent: message.toString()
                });

                const data = JSON.parse(message.toString());
                
                // Log de los datos parseados
                this.logger.info('Datos parseados:', {
                    timestamp: data.timestamp,
                    station_name: data.station_name,
                    variable_name: data.variable_name,
                    valor: data.valor
                });

                if (this.validateStationData(data)) {
                    this.logger.info('Datos validados correctamente, procediendo a procesar');
                    await this.processStationData(data);
                } else {
                    this.logger.warn('Mensaje con formato inválido:', {
                        received: Object.keys(data),
                        required: mqttConfig.messageStructure.stationData.required
                    });
                }
            } catch (error) {
                this.logger.error('Error al procesar mensaje MQTT:', {
                    error: error.message,
                    stack: error.stack,
                    message: message.toString()
                });
            }
        });

        // Evento de error
        this.client.on('error', (err) => {
            this.logger.error('Error de MQTT:', {
                error: err.message,
                stack: err.stack
            });
            this.isConnected = false;
        });

        // Evento de cierre de conexión
        this.client.on('close', () => {
            this.isConnected = false;
            this.logger.warn('Conexión MQTT cerrada');
        });

        // Evento de reconexión
        this.client.on('reconnect', () => {
            this.reconnectAttempts++;
            this.logger.warn(`Intentando reconectar MQTT (intento ${this.reconnectAttempts})`);
            
            if (this.reconnectAttempts >= this.maxReconnectAttempts) {
                this.logger.error('Máximo número de intentos de reconexión alcanzado');
                this.client.end();
            }
        });

        // Evento de desconexión
        this.client.on('offline', () => {
            this.isConnected = false;
            this.logger.warn('Cliente MQTT desconectado');
        });

        // Evento de fin de conexión
        this.client.on('end', () => {
            this.isConnected = false;
            this.logger.info('Conexión MQTT finalizada');
        });
    }

    validateStationData(data) {
        const required = mqttConfig.messageStructure.stationData.required;
        const isValid = required.every(field => {
            const hasField = data.hasOwnProperty(field);
            if (!hasField) {
                this.logger.debug(`Campo requerido faltante: ${field}`);
            }
            return hasField;
        });

        if (!isValid) {
            console.log('Mensaje con formato inválido:', data);
            this.logger.warn('Datos inválidos:', {
                received: Object.keys(data),
                required: required
            });
        }else{
            console.log('Mensaje con formato válido:', data);
        }

        return isValid;
    }

    async processStationData(data) {
        try {
            this.logger.info('Iniciando procesamiento de datos:', {
                station: data.station_name,
                variable: data.variable_name,
                timestamp: data.timestamp
            });
    
            // Validar estación usando el archivo de configuración
            // Si usas array:
            if (!estacionesValidas.includes(data.station_name)) {
                this.logger.warn(`Estación no válida: ${data.station_name}`);
                return;
            }
            

            // 2. Verificar si el dato ya existe
            const isDuplicate = await this.mqttRepository.checkDuplicateData(data);
            this.logger.info(`Verificación de duplicado: ${isDuplicate ? 'es duplicado' : 'no es duplicado'}`);
            
            if (isDuplicate) {
                this.logger.info(`Dato duplicado ignorado: ${JSON.stringify(data)}`);
                return;
            }

            // 3. Guardar el nuevo dato
            const insertId = await this.mqttRepository.saveStationData(data);
            this.logger.info('Datos guardados exitosamente:', {
                insertId,
                station: data.station_name,
                variable: data.variable_name,
                timestamp: data.timestamp
            });
            
        } catch (error) {
            this.logger.error('Error al procesar datos MQTT:', {
                error: error.message,
                stack: error.stack,
                data: data
            });
        }
    }

    // Método para publicar mensajes
    publish(topic, message) {
        if (!this.isConnected) {
            this.logger.warn('Cliente MQTT no conectado. No se puede publicar mensaje.');
            return false;
        }

        try {
            const messageStr = typeof message === 'string' ? message : JSON.stringify(message);
            this.logger.debug('Publicando mensaje:', {
                topic,
                message: messageStr
            });

            this.client.publish(topic, messageStr, (err) => {
                if (err) {
                    this.logger.error(`Error al publicar en ${topic}:`, {
                        error: err.message,
                        message: messageStr
                    });
                } else {
                    this.logger.info(`Mensaje publicado exitosamente en ${topic}:`, message);
                }
            });
            return true;
        } catch (error) {
            this.logger.error('Error al preparar mensaje para publicación:', {
                error: error.message,
                topic,
                message
            });
            return false;
        }
    }

    // Método para verificar el estado de la conexión
    isConnected() {
        return this.isConnected;
    }

    // Método para desconectar el cliente
    disconnect() {
        if (this.client) {
            this.client.end();
            this.logger.info('Cliente MQTT desconectado manualmente');
        }
    }

    // Método para reconectar manualmente
    reconnect() {
        if (this.client) {
            this.client.reconnect();
            this.logger.info('Intentando reconectar manualmente...');
        }
    }

    // Método para obtener estadísticas
    async getStationStats(station_name, startDate, endDate) {
        try {
            this.logger.debug('Obteniendo estadísticas:', {
                station: station_name,
                startDate,
                endDate
            });

            const stats = await this.mqttRepository.getStationStats(station_name, startDate, endDate);
            
            this.logger.info('Estadísticas obtenidas:', {
                station: station_name,
                variables: stats.map(s => s.variable_name)
            });

            return stats;
        } catch (error) {
            this.logger.error('Error al obtener estadísticas:', {
                error: error.message,
                station: station_name,
                startDate,
                endDate
            });
            throw error;
        }
    }

    // Método para obtener datos históricos
    async getHistoricalData(station_name, variable, startDate, endDate) {
        try {
            this.logger.debug('Obteniendo datos históricos:', {
                station: station_name,
                variable,
                startDate,
                endDate
            });

            const data = await this.mqttRepository.getHistoricalData(station_name, variable, startDate, endDate);
            
            this.logger.info('Datos históricos obtenidos:', {
                station: station_name,
                variable,
                count: data.length
            });

            return data;
        } catch (error) {
            this.logger.error('Error al obtener datos históricos:', {
                error: error.message,
                station: station_name,
                variable,
                startDate,
                endDate
            });
            throw error;
        }
    }

    // Método para obtener el último valor
    async getLatestValue(station_name, variable) {
        try {
            this.logger.debug('Obteniendo último valor:', {
                station: station_name,
                variable
            });

            const value = await this.mqttRepository.getLatestValue(station_name, variable);
            
            if (value) {
                this.logger.info('Último valor encontrado:', {
                    station: station_name,
                    variable,
                    timestamp: value.timestamp,
                    valor: value.valor
                });
            } else {
                this.logger.info('No se encontraron valores:', {
                    station: station_name,
                    variable
                });
            }

            return value;
        } catch (error) {
            this.logger.error('Error al obtener último valor:', {
                error: error.message,
                station: station_name,
                variable
            });
            throw error;
        }
    }
}

module.exports = MqttService;