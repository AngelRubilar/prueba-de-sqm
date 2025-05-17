const apiErrorLogger = require('../utils/apiErrorLogger');

class AytErrorHandler {
    handleError(station, response, error) {
        if (error) {
            if (error.response?.status === 401) {
                // Error de autenticación - manejado por el servicio
                return;
            }
            this.handleConnectionError(station, error);
            return;
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
}

module.exports = new AytErrorHandler();