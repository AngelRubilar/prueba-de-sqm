const fs = require('fs');
const path = require('path');
const moment = require('moment-timezone');

class ApiErrorLogger {
    constructor() {
        this.logDir = path.join(__dirname, '../logs/api_errors');
        this.ensureLogDirectoryExists();
    }

    ensureLogDirectoryExists() {
        if (!fs.existsSync(this.logDir)) {
            fs.mkdirSync(this.logDir, { recursive: true });
        }
    }

    getLogFileName() {
        return `api_errors_${moment().tz('America/Santiago').format('YYYY-MM-DD')}.log`;
    }

    logError(apiName, stationName, errorType, errorDetails) {
        const timestamp = moment().tz('America/Santiago').format('YYYY-MM-DD HH:mm:ss');
        const logMessage = `[${timestamp}] API: ${apiName} | Estación: ${stationName} | Tipo: ${errorType} | Detalles: ${errorDetails}`;
        const logFile = path.join(this.logDir, this.getLogFileName());

        // Escribir al archivo
        fs.appendFileSync(logFile, logMessage + '\n');
        
        // MOSTRAR EN CONSOLA también
        console.error(`❌ ${logMessage}`);
    }

    // Método específico para errores de conexión
    logConnectionError(apiName, stationName, error) {
        const statusCode = error.response ? error.response.status : 0;
        const statusText = statusCode === 0 ? 'No responde' : `Error ${statusCode}`;
        const errorMessage = `${statusText} - ${error.message}`;
        this.logError(apiName, stationName, 'Error de conexión', errorMessage);
    }

    // Método específico para error 404
    logNotFoundError(apiName, stationName, error) {
        const errorMessage = `Error 404 - ${error.message}`;
        this.logError(apiName, stationName, 'NOT_FOUND', errorMessage);
    }

    // Método específico para error 500
    logServerError(apiName, stationName, error) {
        const errorMessage = `Error 500 - ${error.message}`;
        this.logError(apiName, stationName, 'SERVER_ERROR', errorMessage);
    }

    // Método específico para error 401
    logAuthError(apiName, stationName, error) {
        const errorMessage = `Error 401 - ${error.message}`;
        this.logError(apiName, stationName, 'AUTH_ERROR', errorMessage);
    }

    // Método específico para respuestas vacías
    logEmptyResponse(apiName, stationName) {
        this.logError(apiName, stationName, 'EMPTY_RESPONSE', 'No se recibieron datos');
    }
}

module.exports = new ApiErrorLogger();