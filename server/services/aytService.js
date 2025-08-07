const axios = require('axios');
const moment = require('moment-timezone');
const aytRepository = require('../repositories/aytRepository');
const nombreVariables = require('../config/nombreVariables');
const aytAuthService = require('./aytAuthService');
const { aytErrorHandler } = require('../errorHandlers');

class AytService {
    constructor() {
        this.isSyncRunning = false; // Flag para evitar ejecuciones simultáneas
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

    // MODIFICAR el método obtenerDatos existente para que sea reutilizable
    async obtenerDatos(tag, fechaDesde = null, fechaHasta = null, soloUltimo = true) {
        const station = this.getStationFromTag(tag);
        
        // Si no se proporcionan fechas, usar las fechas por defecto (día actual + siguiente)
        if (!fechaDesde) {
            fechaDesde = moment().tz('America/Santiago').format('YYYY-MM-DD');
        }
        if (!fechaHasta) {
            fechaHasta = moment().tz('America/Santiago').add(1, 'days').format('YYYY-MM-DD');
        }
        
        console.log(`🔍 Consultando AYT - Estación: ${station}, Tag: ${tag}`);
        console.log(`📅 Rango de fechas: ${fechaDesde} a ${fechaHasta}`);
        console.log(`🕐 Hora actual Chile: ${moment().tz('America/Santiago').format('YYYY-MM-DD HH:mm:ss')}`);

        // Obtener token válido desde el inicio
        let token = await aytAuthService.getValidToken();
        console.log(`🔑 Token AYT obtenido: ${token ? 'Sí' : 'No'}`);
        
        let config = {
            method: 'get',
            maxBodyLength: Infinity,
            url: `http://104.41.40.103:8080/api/Cems/GetBetweenValues?id_cems=01&tag=${tag}&fechaDesde=${fechaDesde}&fechaHasta=${fechaHasta}`,
            headers: {
                'Accept': 'application/json;charset=UTF-8',
                'Authorization': `Bearer ${token}`,
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
            console.log(`✅ AYT API Response - ${tag}:`);
            console.log(`   Status: ${response.status}`);
            console.log(`   Data length: ${datos.length}`);
            
            // Usar el errorHandler para manejar la respuesta
            aytErrorHandler.handleError(station, response);

            // Retornar solo el último o todos según parámetro
            return soloUltimo ? datos[datos.length - 1] : datos;
        } catch (error) {
            const statusCode = error.response?.status;
            
            // MANEJO ESPECÍFICO POR CÓDIGO DE ERROR
            if (statusCode === 401) {
                console.log(`🔄 Error 401 para ${station}, intentando renovar token...`);
                try {
                    // Forzar renovación de token
                    const nuevoToken = await aytAuthService.forceRenewToken();
                    if (!nuevoToken) {
                        aytErrorHandler.handleAuthError(station, new Error('No se pudo obtener un nuevo token'));
                        return soloUltimo ? null : [];
                    }
                    
                    config.headers['Authorization'] = `Bearer ${nuevoToken}`;
                    const response = await axios.request(config);
                    const datos = response.data;
                    console.log(`✅ Reintento exitoso para ${station}`);
                    return soloUltimo ? datos[datos.length - 1] : datos;
                } catch (retryError) {
                    console.error(`❌ Error en reintento para ${station}:`, retryError.message);
                    // Usar el errorHandler para manejar el error de reintento
                    aytErrorHandler.handleError(station, null, retryError);
                    return soloUltimo ? null : [];
                }
            } else if (statusCode === 404) {
                // MANEJO ESPECÍFICO PARA 404
                aytErrorHandler.handleNotFoundError(station, error);
                return soloUltimo ? null : [];
            } else if (statusCode === 500) {
                // MANEJO ESPECÍFICO PARA 500
                aytErrorHandler.handleServerError(station, error);
                return soloUltimo ? null : [];
            } else {
                // Otros errores
                aytErrorHandler.handleError(station, null, error);
                return soloUltimo ? null : [];
            }
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
        // Verificar si hay una sincronización nocturna ejecutándose
        if (this.isSyncRunning) {
            console.log('⚠️ Sincronización nocturna en progreso, saltando consulta normal AYT');
            return [];
        }

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
            try {
                await aytRepository.guardarDatos(resultados);
            } catch (error) {
                console.error('Error al guardar datos en consulta normal:', error.message);
            }
        }

        return resultados;
    }

    // MODIFICAR el método obtenerDatosCompletos para usar el método reutilizable
    async obtenerDatosCompletos(tag) {
        const fechaDesde = moment().tz('America/Santiago').subtract(1, 'days').format('YYYY-MM-DD');
        const fechaHasta = moment().tz('America/Santiago').format('YYYY-MM-DD');
        
        // Reutilizar el método obtenerDatos con parámetros específicos
        return await this.obtenerDatos(tag, fechaDesde, fechaHasta, false); // soloUltimo = false
    }

    // NUEVO: Método para transformar múltiples datos
    transformarDatosCompletos(tag, datosArray) {
        if (!datosArray || datosArray.length === 0) return [];

        const station = this.getStationFromTag(tag);
        const nombreEstandarizado = nombreVariables[tag] || tag;
        const resultados = [];

        for (const datos of datosArray) {
            try {
                const timestamp = moment(datos.timestamp, 'YYYY/MM/DDTHH:mm:ssZ')
                    .tz('America/Santiago')
                    .format('YYYY-MM-DD HH:mm:ss');
                
                if (!timestamp || timestamp === 'Invalid date') continue;

                const valor = datos.value;
                if (valor === undefined || valor === null) continue;

                resultados.push([timestamp, station, nombreEstandarizado, valor]);
            } catch (error) {
                console.error(`Error transformando dato para ${tag}:`, error.message);
                continue;
            }
        }

        console.log(`📊 Transformados ${resultados.length} registros para ${tag}`);
        return resultados;
    }

    // Método auxiliar para filtrar datos nuevos
    filtrarDatosNuevos(datosAPI, datosExistentes) {
        if (!datosExistentes || datosExistentes.length === 0) {
            return datosAPI;
        }

        const timestampsExistentes = new Set(
            datosExistentes.map(row => `${row.timestamp}_${row.station_name}_${row.variable_name}`)
        );

        return datosAPI.filter(([timestamp, station, variable, valor]) => {
            const clave = `${timestamp}_${station}_${variable}`;
            return !timestampsExistentes.has(clave);
        });
    }

    // NUEVO: Método para sincronización nocturna completa
    async sincronizacionNocturna() {
        // Verificar si ya hay una sincronización ejecutándose
        if (this.isSyncRunning) {
            console.log('⚠️ Sincronización nocturna ya está ejecutándose, saltando...');
            return { error: 'Sincronización ya en progreso' };
        }

        this.isSyncRunning = true;
        
        try {
            console.log('🌙 ===== INICIANDO SINCRONIZACIÓN NOCTURNA AYT =====');
            console.log(`🕐 Hora de inicio: ${moment().tz('America/Santiago').format('YYYY-MM-DD HH:mm:ss')}`);

            const allTags = [...this.tagsE6, ...this.tagsE7, ...this.tagsE8];
        const resultados = {
            totalVariables: allTags.length,
            variablesProcesadas: 0,
            totalNuevosRegistros: 0,
            totalRegistrosAPI: 0,
            errores: []
        };

        for (const tag of allTags) {
            try {
                console.log(`🔄 Procesando: ${tag}`);
                
                // 1. Obtener todos los datos de la API
                const datosAPI = await this.obtenerDatosCompletos(tag);
                if (!datosAPI || datosAPI.length === 0) {
                    console.log(`⚠️ No hay datos de API para ${tag}`);
                    continue;
                }

                // 2. Transformar datos
                const datosTransformados = this.transformarDatosCompletos(tag, datosAPI);
                if (datosTransformados.length === 0) {
                    console.log(`⚠️ No hay datos válidos para ${tag}`);
                    continue;
                }

                // 3. Obtener datos existentes de BD
                const station = this.getStationFromTag(tag);
                const nombreEstandarizado = nombreVariables[tag] || tag;
                const datosExistentes = await aytRepository.obtenerDatosExistentes(station, nombreEstandarizado);

                // 4. Filtrar datos nuevos
                const datosNuevos = this.filtrarDatosNuevos(datosTransformados, datosExistentes);
                
                console.log(`📊 ${tag}: ${datosAPI.length} API, ${datosTransformados.length} válidos, ${datosNuevos.length} nuevos`);

                // Pausa antes de guardar para evitar deadlocks
                if (datosNuevos.length > 0) {
                    await new Promise(resolve => setTimeout(resolve, 500));
                }

                // 5. Guardar datos nuevos con manejo de errores mejorado y procesamiento en lotes
                if (datosNuevos.length > 0) {
                    try {
                        // Procesar en lotes de 100 registros para reducir la carga en BD
                        const batchSize = 100;
                        for (let i = 0; i < datosNuevos.length; i += batchSize) {
                            const batch = datosNuevos.slice(i, i + batchSize);
                            await aytRepository.guardarDatos(batch);
                            
                            // Pausa entre lotes
                            if (i + batchSize < datosNuevos.length) {
                                await new Promise(resolve => setTimeout(resolve, 200));
                            }
                        }
                        console.log(`✅ Guardados ${datosNuevos.length} nuevos registros para ${tag} (en lotes)`);
                    } catch (error) {
                        console.error(`❌ Error guardando datos para ${tag}:`, error.message);
                        resultados.errores.push({ tag, error: `Error guardando: ${error.message}` });
                        // Continuar con la siguiente variable en lugar de fallar completamente
                        continue;
                    }
                }

                resultados.variablesProcesadas++;
                resultados.totalNuevosRegistros += datosNuevos.length;
                resultados.totalRegistrosAPI += datosAPI.length;

                // Pausa entre consultas (más larga para evitar deadlocks)
                await new Promise(resolve => setTimeout(resolve, 2000));

            } catch (error) {
                console.error(`❌ Error procesando ${tag}:`, error.message);
                resultados.errores.push({ tag, error: error.message });
            }
        }

        console.log('🌙 ===== RESUMEN SINCRONIZACIÓN NOCTURNA AYT =====');
        console.log(`📊 Variables procesadas: ${resultados.variablesProcesadas}/${resultados.totalVariables}`);
        console.log(`🆕 Nuevos registros: ${resultados.totalNuevosRegistros}`);
        console.log(`📈 Total registros API: ${resultados.totalRegistrosAPI}`);
        console.log(`❌ Errores: ${resultados.errores.length}`);
        console.log(`🕐 Hora de finalización: ${moment().tz('America/Santiago').format('YYYY-MM-DD HH:mm:ss')}`);

        return resultados;
        } catch (error) {
            console.error('❌ Error en sincronización nocturna:', error.message);
            return { error: error.message };
        } finally {
            this.isSyncRunning = false;
        }
    }
}

module.exports = new AytService();