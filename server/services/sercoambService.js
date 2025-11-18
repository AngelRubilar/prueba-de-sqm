const axios = require('axios');
const moment = require('moment-timezone');
const nombreVariables = require('../config/nombreVariables');
const { sercoambErrorHandler } = require('../errorHandlers');
const { createApiCircuitBreaker } = require('../utils/circuitBreaker');
const logger = require('../config/logger');

class SercoambService {
    constructor() {
        // Crear Circuit Breakers para ambas estaciones
        this.circuitBreakerTamentica = createApiCircuitBreaker(
            'SERCOAMB-Tamentica',
            this._makeApiRequestTamentica.bind(this),
            {
                timeout: 15000,
                errorThresholdPercentage: 50,
                resetTimeout: 60000
            }
        );

        this.circuitBreakerVictoria = createApiCircuitBreaker(
            'SERCOAMB-Victoria',
            this._makeApiRequestVictoria.bind(this),
            {
                timeout: 15000,
                errorThresholdPercentage: 50,
                resetTimeout: 60000
            }
        );

        logger.info('SercoambService inicializado con Circuit Breakers para Tamentica y Victoria');

        this.estaciones = {
            tamentica: {
                terminalID: "02054888SKY54C5",
                url: 'http://api.io-sat.cl/IosatApi.asmx/GetData',
                credentials: {
                    usuario: process.env.SERCOAMB_USER_TAMENTICA,
                    pass: process.env.SERCOAMB_PASS_TAMENTICA
                }
            },
            victoria: {
                url: 'https://sercoambvm.uc.r.appspot.com/json/ServicioDataSQM',
                credentials: {
                    user: process.env.SERCOAMB_USER_VICTORIA,
                    password: process.env.SERCOAMB_PASS_VICTORIA,
                    Oricod: "010",
                    Ciacod: "3000028966",
                    LocCod: "011-AIRE-300001",
                    LocUbiNum: "100"
                }
            }
        };
    }

    /**
     * Método privado que realiza la llamada real a la API Tamentica
     * Protegido por Circuit Breaker
     */
    async _makeApiRequestTamentica(config) {
        const response = await axios.request(config);

        // Usar el errorHandler para manejar la respuesta
        sercoambErrorHandler.handleError('Tamentica', response);

        return response.data;
    }

    /**
     * Consultar API Tamentica con Circuit Breaker
     */
    async consultarAPITamentica() {
        try {
            const timestampInicio = moment().subtract(10, 'hours').unix();

            // Verificar que las credenciales existen
            if (!this.estaciones.tamentica.terminalID ||
                !this.estaciones.tamentica.credentials.usuario ||
                !this.estaciones.tamentica.credentials.pass) {
                throw new Error('Faltan credenciales de configuración para Tamentica');
            }

            let config = {
                method: 'post',
                maxBodyLength: Infinity,
                url: this.estaciones.tamentica.url,
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                data: JSON.stringify({
                    terminalID: this.estaciones.tamentica.terminalID,
                    timeStampInicio: timestampInicio,
                    usuario: this.estaciones.tamentica.credentials.usuario,
                    pass: this.estaciones.tamentica.credentials.pass
                })
            };

            // Usar Circuit Breaker para proteger la llamada
            const data = await this.circuitBreakerTamentica.fire(config);

            // Si el Circuit Breaker devolvió fallback
            if (data?.fallback) {
                logger.warn(`SERCOAMB Tamentica API no disponible, usando fallback`);
                return [];
            }

            const filteredData = this.filterDataTamentica(data);
            const transformedData = this.transformarDatosTamentica(filteredData);

            return transformedData;
        } catch (error) {
            // Si el circuito está abierto, retornar array vacío
            if (error.message && error.message.includes('Breaker is open')) {
                logger.warn(`Circuit Breaker abierto para SERCOAMB Tamentica, retornando datos vacíos`);
                return [];
            }

            // Usar el errorHandler para manejar el error
            sercoambErrorHandler.handleError('Tamentica', null, error);
            return [];
        }
    }

    filterDataTamentica(data) {
        if (!Array.isArray(data)) {
            console.log('Los datos recibidos no son un array');
            return [];
        }

        const filtrados = data.filter(item => {
            // Verificar estructura básica
            if (!item || item.tableNumber !== 4 || item.tableSignature !== 20030 || !Array.isArray(item.data)) {
                return false;
            }

            // Verificar si hay al menos un registro válido
            return item.data.some(record => 
                record['Time Of Record'] !== "automataMensajes.wsdl.dataCell" &&
                Object.values(record).some(value => value !== "automataMensajes.wsdl.dataCell")
            );
        });

        console.log(`Datos filtrados: ${filtrados.length} registros válidos de ${data.length} totales`);
        return filtrados;
    }

    transformarDatosTamentica(filteredData) {
       // console.log('=== TRANSFORMACIÓN DE DATOS TAMENTICA ===');
        
        if (filteredData.length === 0) {
            //console.log('No hay datos válidos para transformar en Tamentica');
            return [];
        }
    
        const datosValidos = [];
    
        filteredData.forEach((item, index) => {
           // console.log(`\nProcesando item ${index + 1} de ${filteredData.length}`);
            
            item.data.forEach((record, recordIndex) => {
               // console.log(`\nProcesando registro ${recordIndex}:`, JSON.stringify(record, null, 2));
    
                // Ignorar registros completamente inválidos
                if (record['Time Of Record'] === "automataMensajes.wsdl.dataCell") {
                   // console.log(`Registro ${recordIndex} inválido - Time Of Record inválido`);
                    return;
                }
    
                // Procesar cada campo del registro
                Object.entries(record).forEach(([key, valor]) => {
                    // Ignorar campos de tiempo y registro
                    if (key === 'Time Of Record' || key === 'Record') return;
                    
                    // Ignorar valores inválidos
                    if (valor === "automataMensajes.wsdl.dataCell") return;
    
                    // Obtener el nombre estandarizado del campo usando nombreVariables
                    const nombreEstandarizado = nombreVariables[key];
                    
                    // Si no hay mapeo en nombreVariables, ignorar el campo
                    if (!nombreEstandarizado) {
                        console.log(`Campo ${key} no tiene mapeo en nombreVariables`);
                        return;
                    }
    
                    // Convertir el valor a número
                    const valorNumerico = parseFloat(valor);
                    
                    /* console.log(`Procesando campo: ${key}`);
                    console.log('Valor original:', valor);
                    console.log('Valor numérico:', valorNumerico);
                    console.log('Nombre estandarizado:', nombreEstandarizado);
     */
                    // Solo agregar si el valor es un número válido
                    if (!isNaN(valorNumerico)) {
                        datosValidos.push([
                            moment(record['Time Of Record'], 'DD-MM-YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                            'E9',
                            nombreEstandarizado,
                            valorNumerico
                        ]);
                    }
                });
            });
        });
    
        /* console.log('\n=== DATOS TRANSFORMADOS FINALES ===');
        console.log('Cantidad de registros:', datosValidos.length);
        console.log('Datos transformados:', JSON.stringify(datosValidos, null, 2)); */
        return datosValidos;
    }

    /**
     * Método privado que realiza la llamada real a la API Victoria
     * Protegido por Circuit Breaker
     */
    async _makeApiRequestVictoria(config) {
        const response = await axios.request(config);

        // Usar el errorHandler para manejar la respuesta
        sercoambErrorHandler.handleError('Victoria', response);

        return response.data;
    }

    /**
     * Consultar API Victoria con Circuit Breaker
     */
    async consultarAPIVictoria() {
        try {
            const Fec_Desde = moment().subtract(1, 'days').format('DD-MM-YYYY');
            const Fec_Hasta = moment().format('DD-MM-YYYY');

            const config = {
                method: 'post',
                maxBodyLength: Infinity,
                url: this.estaciones.victoria.url,
                headers: {
                    'Content-Type': 'application/json'
                },
                data: {
                    ...this.estaciones.victoria.credentials,
                    Fec_Desde,
                    Fec_Hasta,
                    Frecuencia: "M"
                }
            };

            // Usar Circuit Breaker para proteger la llamada
            const data = await this.circuitBreakerVictoria.fire(config);

            // Si el Circuit Breaker devolvió fallback
            if (data?.fallback) {
                logger.warn(`SERCOAMB Victoria API no disponible, usando fallback`);
                return [];
            }

            return this.transformarDatosVictoria(data.data.Data);
        } catch (error) {
            // Si el circuito está abierto, retornar array vacío
            if (error.message && error.message.includes('Breaker is open')) {
                logger.warn(`Circuit Breaker abierto para SERCOAMB Victoria, retornando datos vacíos`);
                return [];
            }

            // Usar el errorHandler para manejar el error
            sercoambErrorHandler.handleError('Victoria', null, error);
            return [];
        }
    }

    /**
     * Obtener estadísticas de los Circuit Breakers
     */
    getCircuitBreakerStats() {
        const statsTamentica = this.circuitBreakerTamentica.stats;
        const statsVictoria = this.circuitBreakerVictoria.stats;

        return {
            tamentica: {
                name: 'SERCOAMB-Tamentica',
                state: this.circuitBreakerTamentica.opened ? 'OPEN' : (this.circuitBreakerTamentica.halfOpen ? 'HALF_OPEN' : 'CLOSED'),
                fires: statsTamentica.fires,
                successes: statsTamentica.successes,
                failures: statsTamentica.failures,
                rejects: statsTamentica.rejects,
                timeouts: statsTamentica.timeouts
            },
            victoria: {
                name: 'SERCOAMB-Victoria',
                state: this.circuitBreakerVictoria.opened ? 'OPEN' : (this.circuitBreakerVictoria.halfOpen ? 'HALF_OPEN' : 'CLOSED'),
                fires: statsVictoria.fires,
                successes: statsVictoria.successes,
                failures: statsVictoria.failures,
                rejects: statsVictoria.rejects,
                timeouts: statsVictoria.timeouts
            }
        };
    }

    transformarDatosVictoria(dataArray) {
        if (!Array.isArray(dataArray)) {
            console.log('Los datos de Victoria no son un array');
            return [];
        }

        return dataArray
            .filter(item => {
                // Validar que existan todos los campos necesarios
                if (!item.TmStamp || !item.Variable || item.Variable.Valor === undefined) {
                    return false;
                }
                // Validar que el valor sea un número válido
                const valor = parseFloat(item.Variable.Valor);
                return !isNaN(valor) && isFinite(valor);
            })
            .map(item => {
                const timestamp = moment(item.TmStamp);
                if (!timestamp.isValid()) return null;

                return [
                    timestamp.format('YYYY-MM-DD HH:mm:ss'),
                    'E10',
                    nombreVariables[item.Variable.Descripcion] || item.Variable.Descripcion,
                    parseFloat(item.Variable.Valor)
                ];
            })
            .filter(item => item !== null);
    }

    async obtenerTodosLosDatos() {
        try {
            const [datosTamentica, datosVictoria] = await Promise.all([
                this.consultarAPITamentica(),
                this.consultarAPIVictoria()
            ]);

            return {
                tamentica: datosTamentica || [],
                victoria: datosVictoria || []
            };
        } catch (error) {
            console.error('Error al obtener todos los datos:', error.message);
            return {
                tamentica: [],
                victoria: []
            };
        }
    }
}

module.exports = new SercoambService();