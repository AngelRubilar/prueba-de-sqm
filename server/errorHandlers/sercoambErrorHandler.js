const apiErrorLogger = require('../utils/apiErrorLogger');

class SercoambErrorHandler {
    handleError(station, response, error) {
        if (error) {
            this.handleConnectionError(station, error);
            return;
        }

        if (!response?.data || response.data.length === 0) {
            this.handleEmptyResponse(station);
            return;
        }

        if (station === 'Tamentica') {
            this.handleTamenticaData(station, response.data);
        } else if (station === 'Victoria') {
            this.handleVictoriaData(station, response.data);
        }
    }

    handleConnectionError(station, error) {
        apiErrorLogger.logConnectionError('Sercoamb', station, error);
    }

    handleEmptyResponse(station) {
        apiErrorLogger.logEmptyResponse('Sercoamb', station);
    }

    handleTamenticaData(station, data) {
        const todosInvalidos = data.every(item => 
            item.data.every(record => 
                record['Time Of Record'] === "automataMensajes.wsdl.dataCell"
            )
        );
        if (todosInvalidos) {
            this.handleInvalidData(station);
        }
    }

    handleVictoriaData(station, data) {
        if (!data?.data?.Data) {
            this.handleEmptyResponse(station);
        }
    }

    handleInvalidData(station) {
        apiErrorLogger.logConnectionError('Sercoamb', station, {
            errorType: 'INVALID_DATA',
            message: 'Todos los datos recibidos son inválidos'
        });
    }
}

module.exports = new SercoambErrorHandler();