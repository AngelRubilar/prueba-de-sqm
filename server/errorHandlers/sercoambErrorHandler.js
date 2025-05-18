const apiErrorLogger = require('../utils/apiErrorLogger');

class SercoambErrorHandler {
    constructor() {
        this.lastTimestamps = new Map(); // Almacena los últimos timestamps de cada estación
    }

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
            return;
        }

        // Verificar datos estancados
        const lastRecord = data[data.length - 1];
        if (lastRecord?.TmStamp) {
            this.checkStaleData(station, lastRecord.TmStamp);
        }
    }

    handleVictoriaData(station, data) {
        if (!data?.data?.Data) {
            this.handleEmptyResponse(station);
            return;
        }

        // Verificar datos estancados
        const lastRecord = data.data.Data[data.data.Data.length - 1];
        if (lastRecord?.TmStamp) {
            this.checkStaleData(station, lastRecord.TmStamp);
        }
    }

    handleInvalidData(station) {
        apiErrorLogger.logConnectionError('Sercoamb', station, {
            errorType: 'INVALID_DATA',
            message: 'Todos los datos recibidos son inválidos'
        });
    }

    checkStaleData(station, currentTimestamp) {
        const lastTimestamp = this.lastTimestamps.get(station);
        
        if (lastTimestamp && lastTimestamp === currentTimestamp) {
            apiErrorLogger.logConnectionError('Sercoamb', station, {
                errorType: 'STALE_DATA',
                message: `Los datos no se han actualizado. Último timestamp: ${currentTimestamp}`,
                timestamp: currentTimestamp
            });
        } else {
            this.lastTimestamps.set(station, currentTimestamp);
        }
    }
}

module.exports = new SercoambErrorHandler();