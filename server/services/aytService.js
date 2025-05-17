const axios = require('axios');
const moment = require('moment-timezone');
const aytRepository = require('../repositories/aytRepository');
const nombreVariables = require('../config/nombreVariables');
const aytAuthService = require('./aytAuthService');
const { aytErrorHandler } = require('../errorHandlers');

class AytService {
    constructor() {
        this.tagsE6 = [
            'VELOCIDAD_DEL_VIENTO_M/S_HUARA_MIN',
            'DIRECCION_DEL_VIENTO_HUARA_MIN',
            'PM10_HUARA_MIN',
            'HUMEDAD_PORCENTAJE_HUARA_MIN',
            'TEMPERATURA_C_HUARA_MIN',
            'RADIACION_W/m2_HUARA_MIN',
            'PLUVIOMETRO_MM_HUARA_MIN',
            'PRESION_ATM_hPa_HUARA_MIN',
            'SO2_PPBV_HUARA_MIN',
            'NO_PPBV_HUARA_MIN',
            'NOX_PPBV_HUARA_MIN',
            'CO_PPMV_HUARA_MIN',
            'O3_HUARA_MIN'
        ];

        this.tagsE7 = [
            'VELOCIDAD_DEL_VIENTO_M/S_EXOFVICTORIA_MIN',
            'DIRECCION_DEL_VIENTO_EXOFVICTORIA_MIN',
            'PM10_EXOFVICTORIA_MIN',
            'PM2.5_EXOFVICTORIA_MIN',
            'HUMEDAD_PORCENTAJE_EXOFVICTORIA_MIN',
            'TEMPERATURA_C_EXOFVICTORIA_MIN',
            'PRESION_ATM_hPa_EXOFVICTORIA_MIN',
            'SO2_PPBV_EXOFVICTORIA_MIN',
            'NO_PPBV_EXOFVICTORIA_MIN',
            'NOX_PPBV_EXOFVICTORIA_MIN',
            'CO_PPMV_EXOFVICTORIA_MIN'
        ];

        this.tagsE8 = [
            'DIRECCION_DEL_VIENTO_COLPINTADOS_MIN',
            'PM10_COLPINTADOS_MIN',
            'PM2.5_COLPINTADOS_MIN',
            'HUMEDAD_PORCENTAJE_COLPINTADOS_MIN',
            'TEMPERATURA_C_COLPINTADOS_MIN',
            'PRESION_ATM_hPa_COLPINTADOS_MIN',
            'SO2_PPBV_COLPINTADOS_MIN',
            'NO_PPBV_COLPINTADOS_MIN',
            'NOX_PPBV_COLPINTADOS_MIN',
            'CO_PPMV_COLPINTADOS_MIN'
        ];
    }

    getStationFromTag(tag) {
        if (this.tagsE6.includes(tag)) return 'E6';
        if (this.tagsE7.includes(tag)) return 'E7';
        if (this.tagsE8.includes(tag)) return 'E8';
        return 'Unknown';
    }

    async obtenerDatos(tag) {
        const station = this.getStationFromTag(tag);
        const fechaDesde = moment().format('YYYY-MM-DD');
        const fechaHasta = moment().add(1, 'days').format('YYYY-MM-DD');

        let config = {
            method: 'get',
            maxBodyLength: Infinity,
            url: `http://104.41.40.103:8080/api/Cems/GetBetweenValues?id_cems=01&tag=${tag}&fechaDesde=${fechaDesde}&fechaHasta=${fechaHasta}`,
            headers: {
                'Accept': 'application/json;charset=UTF-8',
                'Authorization': `Bearer ${process.env.AYT_TOKEN}`,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                usuario: process.env.AYT_USER,
                password: process.env.AYT_PASS
            })
        };

        try {
            let response = await axios.request(config);
            const datos = response.data;
            
            // Usar el errorHandler para manejar la respuesta
            aytErrorHandler.handleError(station, response);

            return datos[datos.length - 1];
        } catch (error) {
            if (error.response?.status === 401) {
                try {
                    const nuevoToken = await aytAuthService.getValidToken();
                    if (!nuevoToken) {
                        throw new Error('No se pudo obtener un nuevo token');
                    }
                    
                    config.headers['Authorization'] = `Bearer ${nuevoToken}`;
                    const response = await axios.request(config);
                    const datos = response.data;
                    return datos[datos.length - 1];
                } catch (retryError) {
                    // Usar el errorHandler para manejar el error de reintento
                    aytErrorHandler.handleError(station, null, retryError);
                    return null;
                }
            }
            
            // Usar el errorHandler para manejar el error original
            aytErrorHandler.handleError(station, null, error);
            return null;
        }
    }

    transformarRespuesta(tag, datos) {
        if (!datos) return null;

        try {
            const station = this.getStationFromTag(tag);
            const timestamp = moment(datos.timestamp, 'YYYY/MM/DDTHH:mm:ssZ')
                .tz('America/Santiago')
                .format('YYYY-MM-DD HH:mm:ss');
            
            if (!timestamp || timestamp === 'Invalid date') return null;

            const valor = datos.value;
            const nombreEstandarizado = nombreVariables[tag] || tag;

            return [timestamp, station, nombreEstandarizado, valor];
        } catch (error) {
            return null;
        }
    }

    async obtenerDatosParaTodosLosTags() {
        const resultados = [];
        const allTags = [...this.tagsE6, ...this.tagsE7, ...this.tagsE8];

        for (const tag of allTags) {
            try {
                const datos = await this.obtenerDatos(tag);
                if (datos) {
                    const datosTransformados = this.transformarRespuesta(tag, datos);
                    if (datosTransformados) {
                        resultados.push(datosTransformados);
                    }
                }
            } catch (error) {
                const station = this.getStationFromTag(tag);
                // Usar el errorHandler para manejar el error
                aytErrorHandler.handleError(station, null, error);
                continue;
            }
        }

        if (resultados.length > 0) {
            await aytRepository.guardarDatos(resultados);
        }

        return resultados;
    }
}

module.exports = new AytService();