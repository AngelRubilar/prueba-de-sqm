const AverageService = require('../services/averageService');

class AverageController {
    constructor() {
        try {
            this.averageService = new AverageService();
            console.log('✅ AverageController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error inicializando AverageController:', error);
            this.averageService = null;
        }
    }

    /**
     * Obtiene los últimos promedios para todas las estaciones configuradas
     */
    async obtenerUltimosPromedios(req, res) {
        try {
            console.log('🔍 DEBUG: Iniciando obtenerUltimosPromedios');
            
            // Verificar que el servicio esté inicializado
            if (!this.averageService) {
                console.error('❌ AverageService no está inicializado');
                return res.status(500).json({
                    success: false,
                    message: 'Error al obtener promedios',
                    error: 'AverageService no está inicializado correctamente'
                });
            }
            
            // Obtener configuración de variables por estación
            const configuracion = await this.averageService.obtenerConfiguracionVariables();
            console.log(`🔍 DEBUG: Configuración obtenida: ${configuracion.length} variables`);
            
            // Obtener últimos promedios para todas las configuraciones
            const promedios = await this.averageService.obtenerPromediosMultiples(configuracion);
            console.log(`🔍 DEBUG: Promedios obtenidos: ${promedios.length} registros`);
            
            // Formatear respuesta para el frontend
            const respuestaFormateada = this.formatearPromediosParaFrontend(promedios);
            console.log(`🔍 DEBUG: Respuesta formateada: ${Object.keys(respuestaFormateada).length} estaciones`);
            
            res.json({
                success: true,
                data: respuestaFormateada,
                timestamp: new Date().toISOString()
            });
            
        } catch (error) {
            console.error('Error obteniendo últimos promedios:', error);
            res.status(500).json({
                success: false,
                message: 'Error al obtener promedios',
                error: error.message
            });
        }
    }

    /**
     * Obtiene promedios para una estación específica
     */
    async obtenerPromediosEstacion(req, res) {
        try {
            const { stationName } = req.params;
            
            if (!stationName) {
                return res.status(400).json({
                    success: false,
                    message: 'Nombre de estación requerido'
                });
            }

            // Obtener configuración para la estación
            const configuracion = await this.averageService.obtenerConfiguracionVariables();
            const configEstacion = configuracion.filter(config => config.station_name === stationName);
            
            if (configEstacion.length === 0) {
                return res.status(404).json({
                    success: false,
                    message: `No se encontró configuración para la estación ${stationName}`
                });
            }

            // Obtener promedios para la estación
            const promedios = await this.averageService.obtenerPromediosMultiples(configEstacion);
            
            res.json({
                success: true,
                station: stationName,
                data: promedios,
                timestamp: new Date().toISOString()
            });
            
        } catch (error) {
            console.error('Error obteniendo promedios de estación:', error);
            res.status(500).json({
                success: false,
                message: 'Error al obtener promedios de estación',
                error: error.message
            });
        }
    }

    /**
     * Obtiene promedios históricos para una estación y variable
     */
    async obtenerPromediosHistoricos(req, res) {
        try {
            const { stationName, variableName } = req.params;
            const { fechaInicio, fechaFin } = req.query;
            
            if (!stationName || !variableName) {
                return res.status(400).json({
                    success: false,
                    message: 'Nombre de estación y variable requeridos'
                });
            }

            // Validar fechas
            const fechaInicioValida = fechaInicio || new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            const fechaFinValida = fechaFin || new Date().toISOString().split('T')[0];

            const promedios = await this.averageService.obtenerPromediosHistoricos(
                stationName, 
                variableName, 
                fechaInicioValida, 
                fechaFinValida
            );
            
            res.json({
                success: true,
                station: stationName,
                variable: variableName,
                fechaInicio: fechaInicioValida,
                fechaFin: fechaFinValida,
                data: promedios,
                timestamp: new Date().toISOString()
            });
            
        } catch (error) {
            console.error('Error obteniendo promedios históricos:', error);
            res.status(500).json({
                success: false,
                message: 'Error al obtener promedios históricos',
                error: error.message
            });
        }
    }

    /**
     * Ejecuta cálculo manual de promedios
     */
    async ejecutarCalculoManual(req, res) {
        try {
            await this.scheduler.ejecutarCalculoManual();
            
            res.json({
                success: true,
                message: 'Cálculo de promedios ejecutado manualmente',
                timestamp: new Date().toISOString()
            });
            
        } catch (error) {
            console.error('Error ejecutando cálculo manual:', error);
            res.status(500).json({
                success: false,
                message: 'Error al ejecutar cálculo manual',
                error: error.message
            });
        }
    }

    /**
     * Obtiene el estado del scheduler
     */
    async obtenerEstadoScheduler(req, res) {
        try {
            const AverageScheduler = require('../services/averageScheduler');
            const scheduler = new AverageScheduler();
            const estado = scheduler.getStatus();
            
            res.json({
                success: true,
                data: estado,
                timestamp: new Date().toISOString()
            });
            
        } catch (error) {
            console.error('Error obteniendo estado del scheduler:', error);
            res.status(500).json({
                success: false,
                message: 'Error al obtener estado del scheduler',
                error: error.message
            });
        }
    }

    /**
     * Inicia el scheduler
     */
    async iniciarScheduler(req, res) {
        try {
            const AverageScheduler = require('../services/averageScheduler');
            const scheduler = new AverageScheduler();
            scheduler.start();
            
            res.json({
                success: true,
                message: 'Scheduler iniciado',
                timestamp: new Date().toISOString()
            });
            
        } catch (error) {
            console.error('Error iniciando scheduler:', error);
            res.status(500).json({
                success: false,
                message: 'Error al iniciar scheduler',
                error: error.message
            });
        }
    }

    /**
     * Detiene el scheduler
     */
    async detenerScheduler(req, res) {
        try {
            const AverageScheduler = require('../services/averageScheduler');
            const scheduler = new AverageScheduler();
            scheduler.stop();
            
            res.json({
                success: true,
                message: 'Scheduler detenido',
                timestamp: new Date().toISOString()
            });
            
        } catch (error) {
            console.error('Error deteniendo scheduler:', error);
            res.status(500).json({
                success: false,
                message: 'Error al detener scheduler',
                error: error.message
            });
        }
    }

    /**
     * Formatea los promedios para el frontend
     */
    formatearPromediosParaFrontend(promedios) {
        console.log(`🔍 DEBUG: Formateando ${promedios.length} promedios`);
        const formateado = {};
        
        promedios.forEach(promedio => {
            const { station_name, variable_name, promedio_hora, promedio_dia, fecha_creacion, hora_calculo } = promedio;
            
            console.log(`🔍 DEBUG: Procesando ${station_name} ${variable_name}: hora=${promedio_hora}, dia=${promedio_dia}`);
            
            if (!formateado[station_name]) {
                formateado[station_name] = {};
            }
            
            formateado[station_name][variable_name] = {
                valor: promedio_dia, // Valor principal: promedio diario (24 horas del día)
                promedioHora: promedio_hora, // Promedio de la última hora
                promedioDiario: promedio_dia, // Promedio diario (24 horas del día actual)
                fechaCreacion: fecha_creacion,
                horaCalculo: hora_calculo
            };
        });
        
        console.log(`🔍 DEBUG: Formateado completado. Estaciones: ${Object.keys(formateado).join(', ')}`);
        return formateado;
    }

    /**
     * Obtiene la configuración de variables por estación
     */
    async obtenerConfiguracion(req, res) {
        try {
            const configuracion = await this.averageService.obtenerConfiguracionVariables();
            
            // Agrupar por estación
            const configuracionAgrupada = {};
            configuracion.forEach(config => {
                if (!configuracionAgrupada[config.station_name]) {
                    configuracionAgrupada[config.station_name] = [];
                }
                configuracionAgrupada[config.station_name].push(config.variable_name);
            });
            
            res.json({
                success: true,
                data: configuracionAgrupada,
                timestamp: new Date().toISOString()
            });
            
        } catch (error) {
            console.error('Error obteniendo configuración:', error);
            res.status(500).json({
                success: false,
                message: 'Error al obtener configuración',
                error: error.message
            });
        }
    }
}

module.exports = AverageController; 