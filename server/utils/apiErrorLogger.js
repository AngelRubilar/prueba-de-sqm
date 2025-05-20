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
        const logMessage = `[${timestamp}] API: ${apiName} | Estación: ${stationName} | Tipo: ${errorType} | Detalles: ${errorDetails}\n`;
        const logFile = path.join(this.logDir, this.getLogFileName());

        fs.appendFileSync(logFile, logMessage);
    }

    // Método específico para errores de conexión
    logConnectionError(apiName, stationName, error) {
        const statusCode = error.response ? error.response.status : 0;
        const statusText = statusCode === 0 ? 'No responde' : `Error ${statusCode}`;
        const errorMessage = `${statusText} - ${error.message}`;
        this.logError(apiName, stationName, 'Error de conexión', errorMessage);
    }

    // Método específico para respuestas vacías
    logEmptyResponse(apiName, stationName) {
        this.logError(apiName, stationName, 'EMPTY_RESPONSE', 'No se recibieron datos');
    }
}

module.exports = new ApiErrorLogger();