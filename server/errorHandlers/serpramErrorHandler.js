const apiErrorLogger = require('../utils/apiErrorLogger');

class SerpramErrorHandler {
    handleError(dispositivo, response, error) {
        if (error) {
            this.handleConnectionError(dispositivo, error);
            return;
        }

        if (!response?.data?.resultado) {
            this.handleEmptyResponse(dispositivo);
            return;
        }

        const resultado = response.data.resultado;
        if (resultado.length === 0 || this.isDataEmpty(resultado)) {
            this.handleNoDataReceived(dispositivo, response.data);
        }
    }

    handleConnectionError(dispositivo, error) {
        apiErrorLogger.logConnectionError('Serpram', dispositivo, error);
    }

    handleEmptyResponse(dispositivo) {
        apiErrorLogger.logEmptyResponse('Serpram', dispositivo);
    }

    handleNoDataReceived(dispositivo, data) {
        apiErrorLogger.logConnectionError('Serpram', dispositivo, {
            errorType: 'NO_DATA_RECEIVED',
            message: 'La respuesta del servicio indica que no se recibieron datos',
            data: data
        });
    }

    isDataEmpty(resultado) {
        return resultado.every(item => 
            item !== null && 
            typeof item === 'object' && 
            !Array.isArray(item) &&
            item.dispositivoId === "" &&
            Array.isArray(item.parametros) &&
            item.parametros.length === 0
        );
    }
}

module.exports = new SerpramErrorHandler();