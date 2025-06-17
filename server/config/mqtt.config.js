// En mqtt.config.js
module.exports = {
    server: {
        port: process.env.MQTT_PORT || 1883,
        host: process.env.MQTT_HOST || 'localhost'
    },
    topics: {
        STATION_DATA: 'station/data'  // Tópico principal para datos de estaciones
    },
    messageStructure: {
        stationData: {
            required: ['timestamp', 'station_name', 'variable_name', 'valor']
        }
    }
};