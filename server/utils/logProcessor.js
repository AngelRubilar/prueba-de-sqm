const fs = require('fs');
const path = require('path');
const readline = require('readline');
const moment = require('moment-timezone');
const createCsvWriter = require('csv-writer').createObjectCsvWriter;

class LogProcessor {
    constructor() {
        this.logDir = path.join(__dirname, '../logs/api_errors');
        this.totalReferencia = 1440;
        this.estacionesAGrupar = ['E6', 'E7', 'E8'];
        this.apiAGrupar = 'AYT';
    }

    async procesarLog() {
        try {
            // Obtener la fecha de ayer en formato YYYY-MM-DD usando la zona horaria de Chile
            const fechaAyer = moment().tz('America/Santiago').subtract(1, 'days').format('YYYY-MM-DD');
            const nombreArchivo = `api_errors_${fechaAyer}.log`;
            const rutaArchivo = path.join(this.logDir, nombreArchivo);
            
            console.log(`Buscando archivo de log: ${nombreArchivo}`);
            
            if (!fs.existsSync(rutaArchivo)) {
                throw new Error(`No se encontró el archivo "${nombreArchivo}"`);
            }

            console.log(`Procesando archivo de log: ${nombreArchivo}`);

            const groupedErrors = {};
            const individualErrors = [];
            const metrics = {
                totalErrors: 0,
                errorsByType: {},
                errorsByApi: {},
                errorsByStation: {},
                hourlyDistribution: {},
                mostCommonErrors: []
            };

            const fileStream = fs.createReadStream(rutaArchivo, { encoding: 'utf-8' });
            const rl = readline.createInterface({
                input: fileStream,
                crlfDelay: Infinity,
            });

            for await (const line of rl) {
                const match = line.match(/\[(.*?)\] API: (.*?) \| Estación: (.*?) \| Tipo: (.*?) \| Detalles: (.*)/);
                if (match) {
                    const [timestampStr, api, estacion, tipo, detalles] = match.slice(1);
                    const estacionStrip = estacion.trim();
                    const apiStrip = api.trim();

                    if (estacionStrip !== 'SQM Baquedano') {
                        const timestamp = moment(timestampStr, 'YYYY-MM-DD HH:mm:ss');
                        const minuteKey = timestamp.format('YYYY-MM-DD HH:mm');
                        const hour = timestamp.format('HH:00');
                        const errorKey = `${minuteKey}-${estacionStrip}-${tipo.trim()}-${detalles.trim()}`;

                        // Actualizar métricas generales
                        metrics.totalErrors++;
                        metrics.errorsByType[tipo] = (metrics.errorsByType[tipo] || 0) + 1;
                        metrics.errorsByApi[apiStrip] = (metrics.errorsByApi[apiStrip] || 0) + 1;
                        metrics.errorsByStation[estacionStrip] = (metrics.errorsByStation[estacionStrip] || 0) + 1;
                        metrics.hourlyDistribution[hour] = (metrics.hourlyDistribution[hour] || 0) + 1;

                        if (this.estacionesAGrupar.includes(estacionStrip) && apiStrip === this.apiAGrupar) {
                            if (!groupedErrors[errorKey]) {
                                groupedErrors[errorKey] = {
                                    Minuto: minuteKey,
                                    Estación: estacionStrip,
                                    API: apiStrip,
                                    Tipo: tipo.trim(),
                                    Detalles: detalles.trim(),
                                };
                            }
                        } else {
                            individualErrors.push({
                                Minuto: minuteKey,
                                Timestamp: timestampStr,
                                API: apiStrip,
                                Estación: estacionStrip,
                                Tipo: tipo.trim(),
                                Detalles: detalles.trim(),
                            });
                        }
                    }
                }
            }

            // Calcular los errores más comunes
            metrics.mostCommonErrors = Object.entries(metrics.errorsByType)
                .sort(([,a], [,b]) => b - a)
                .slice(0, 5)
                .map(([type, count]) => ({ type, count }));

            const groupedErrorsList = Object.values(groupedErrors);
            const allErrors = [...groupedErrorsList, ...individualErrors];

            const erroresPorEstacion = allErrors.reduce((acc, error) => {
                acc[error.Estación] = (acc[error.Estación] || 0) + 1;
                return acc;
            }, {});

            const erroresConPorcentaje = Object.entries(erroresPorEstacion).map(([estacion, totalErrores]) => ({
                Estación: estacion,
                TotalErrores: totalErrores,
                PorcentajeError: parseFloat(((totalErrores / this.totalReferencia) * 100).toFixed(2)),
            }));

            const finalErrors = allErrors.map(error => {
                const porcentajeInfo = erroresConPorcentaje.find(item => item.Estación === error.Estación);
                return {
                    ...error,
                    TotalErrores: porcentajeInfo ? porcentajeInfo.TotalErrores : 0,
                    PorcentajeError: porcentajeInfo ? porcentajeInfo.PorcentajeError : 0,
                };
            });

            // Generar archivos CSV con la fecha de ayer
            const csvWriter = createCsvWriter({
                path: path.join(this.logDir, `errores_con_porcentaje_${fechaAyer}.csv`),
                header: [
                    { id: 'Minuto', title: 'Minuto' },
                    { id: 'Timestamp', title: 'Timestamp' },
                    { id: 'API', title: 'API' },
                    { id: 'Estación', title: 'Estación' },
                    { id: 'Tipo', title: 'Tipo' },
                    { id: 'Detalles', title: 'Detalles' },
                    { id: 'TotalErrores', title: 'Total Errores' },
                    { id: 'PorcentajeError', title: 'Porcentaje Error' }
                ]
            });

            await csvWriter.writeRecords(finalErrors);
            
            const csvWriterResumen = createCsvWriter({
                path: path.join(this.logDir, `resumen_errores_${fechaAyer}.csv`),
                header: [
                    { id: 'Estación', title: 'Estación' },
                    { id: 'TotalErrores', title: 'Total Errores' },
                    { id: 'PorcentajeError', title: 'Porcentaje Error' }
                ]
            });

            await csvWriterResumen.writeRecords(erroresConPorcentaje);

            console.log(`Se han generado los archivos CSV para la fecha ${fechaAyer}`);

            return {
                metrics: {
                    ...metrics,
                    totalReferencia: this.totalReferencia,
                    erroresConPorcentaje: erroresConPorcentaje  // agregamos para que se pueda usar en el logAnalyzer
                },
                erroresDetallados: finalErrors,
                erroresPorEstacion: erroresConPorcentaje,
                fecha: fechaAyer
            };

        } catch (error) {
            console.error(`Error al procesar el archivo de log: ${error.message}`);
            throw error;
        }
    }
}

module.exports = new LogProcessor();