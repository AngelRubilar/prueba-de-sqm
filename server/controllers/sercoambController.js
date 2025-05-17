const sercoambService = require('../services/sercoambService');
const dataRepository = require('../repositories/dataRepository');

class SercoambController {
    async realizarConsulta() {
        try {
            // Consultar Tamentica (E9)
            console.log('Procesando estación Tamentica (E9)');
            const datosTamentica = await sercoambService.consultarAPITamentica();
            if (datosTamentica && datosTamentica.length > 0) {
                await dataRepository.guardarDatosEnMySQL(datosTamentica);
            }

            // Consultar Victoria (E10)
            console.log('Procesando estación Victoria (E10)');
            const datosVictoria = await sercoambService.consultarAPIVictoria();
            if (datosVictoria && datosVictoria.length > 0) {
                await dataRepository.guardarDatosEnMySQL(datosVictoria);
            }
        } catch (error) {
            console.error('Error en la consulta de Sercoamb:', error.message);
            throw error;
        }
    }
}

module.exports = new SercoambController();