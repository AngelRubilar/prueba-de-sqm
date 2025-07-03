const apiErrorLogger = require('../utils/apiErrorLogger');

class AytErrorHandler {
    handleError(station, response, error) {
        if (error) {
            const statusCode = error.response?.status;
            
            // MANEJO ESPECÍFICO POR CÓDIGO DE ERROR
            switch (statusCode) {
                case 401:
                    // Error de autenticación - manejado por el servicio
                    apiErrorLogger.logAuthError('AYT', station, error);
                    return;
                case 404:
                    // Error de recurso no encontrado
                    apiErrorLogger.logNotFoundError('AYT', station, error);
                    return;
                case 500:
                    // Error del servidor
                    apiErrorLogger.logServerError('AYT', station, error);
                    return;
                default:
                    // Otros errores de conexión
                    this.handleConnectionError(station, error);
                    return;
            }
        }

        if (!response?.data || response.data.length === 0) {
            this.handleEmptyResponse(station);
        }
    }

    handleConnectionError(station, error) {
        apiErrorLogger.logConnectionError('AYT', station, error);
    }

    handleEmptyResponse(station) {
        apiErrorLogger.logEmptyResponse('AYT', station);
    }

    // Método específico para error 404
    handleNotFoundError(station, error) {
        apiErrorLogger.logNotFoundError('AYT', station, error);
    }

    // Método específico para error 500
    handleServerError(station, error) {
        apiErrorLogger.logServerError('AYT', station, error);
    }

    // Método específico para error 401
    handleAuthError(station, error) {
        apiErrorLogger.logAuthError('AYT', station, error);
    }
}

module.exports = new AytErrorHandler();