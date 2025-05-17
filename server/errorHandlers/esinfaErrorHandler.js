const apiErrorLogger = require('../utils/apiErrorLogger');

class EsinfaErrorHandler {
    handleError(response, error) {
        if (error) {
            if (error.response?.status === 500) {
                // Error 500 - manejado por el servicio para renovar token
                return;
            }
            this.handleConnectionError(error);
            return;
        }

        if (!response?.data || response.data.length === 0) {
            this.handleEmptyResponse();
        }
    }

    handleConnectionError(error) {
        apiErrorLogger.logConnectionError('Esinfa', 'Hospital', error);
    }

    handleEmptyResponse() {
        apiErrorLogger.logEmptyResponse('Esinfa', 'Hospital');
    }
}

module.exports = new EsinfaErrorHandler();