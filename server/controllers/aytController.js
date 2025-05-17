const aytService = require('../services/aytService');

class AytController {
    async obtenerDatosEstaciones(req, res) {
        try {
            const resultados = await aytService.obtenerDatosParaTodosLosTags();
            res.json({
                success: true,
                message: 'Datos obtenidos y guardados correctamente',
                data: resultados
            });
        } catch (error) {
            console.error('Error en el controlador de AYT:', error);
            res.status(500).json({
                success: false,
                message: 'Error al obtener los datos de las estaciones',
                error: error.message
            });
        }
    }
    // Nuevo método para ejecuciones programadas
    async obtenerDatosEstacionesProgramado() {
        try {
            const resultados = await aytService.obtenerDatosParaTodosLosTags();
            console.log('Datos de AYT obtenidos y guardados correctamente');
            return resultados;
        } catch (error) {
            console.error('Error en el controlador de AYT:', error);
            throw error;
        }
    }
}

module.exports = new AytController(); 