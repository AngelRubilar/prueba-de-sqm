const axios = require('axios');
const moment = require('moment-timezone');
const nombreVariables = require('../config/nombreVariables');
const { sercoambErrorHandler } = require('../errorHandlers');

class SercoambService {
    constructor() {
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

    async consultarAPITamentica() {
        try {
            const timestampInicio = moment().subtract(10, 'hours').unix();
            
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

            const response = await axios.request(config);
            
            // Usar el errorHandler para manejar la respuesta
            sercoambErrorHandler.handleError('Tamentica', response);

            const filteredData = this.filterDataTamentica(response.data);
            return this.transformarDatosTamentica(filteredData);
        } catch (error) {
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
        // Si no hay datos válidos, retornar array vacío
        if (filteredData.length === 0) {
            console.log('No hay datos válidos para transformar en Tamentica');
            return [];
        }

        const datosValidos = [];

        filteredData.forEach(item => {
            item.data.forEach(record => {
                // Ignorar registros completamente inválidos
                if (record['Time Of Record'] === "automataMensajes.wsdl.dataCell") {
                    return;
                }

                // Solo procesar registros con al menos algunos datos válidos
                const tieneValoresValidos = Object.values(record).some(value => 
                    value !== "automataMensajes.wsdl.dataCell"
                );

                if (!tieneValoresValidos) {
                    return;
                }

                // Procesar los datos válidos
                Object.entries(record).forEach(([key, valor]) => {
                    if (key === 'Time Of Record' || key === 'Record') return;
                    if (valor === "automataMensajes.wsdl.dataCell") return;

                    const nombreEstandarizado = nombreVariables[key] || key;
                    datosValidos.push([
                        moment().format('YYYY-MM-DD HH:mm:ss'),
                        'E9',
                        nombreEstandarizado,
                        0
                    ]);
                });
            });
        });

        return datosValidos;
    }

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

            const response = await axios.request(config);
            
            // Usar el errorHandler para manejar la respuesta
            sercoambErrorHandler.handleError('Victoria', response);

            return this.transformarDatosVictoria(response.data.data.Data);
        } catch (error) {
            // Usar el errorHandler para manejar el error
            sercoambErrorHandler.handleError('Victoria', null, error);
            return [];
        }
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