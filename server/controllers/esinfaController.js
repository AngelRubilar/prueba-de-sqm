const esinfaService = require('../services/esinfaService');
const esinfaRepository = require('../repositories/esinfaRepository');

class EsinfaController {
  async realizarConsulta() {
    try {
      console.log('Iniciando consulta Esinfa...');
      const datosArray = await esinfaService.consultarAPI();
      await esinfaRepository.guardarDatosEsinfa(datosArray);
      console.log('Consulta Esinfa completada exitosamente');
    } catch (error) {
      console.error('Error en la consulta Esinfa:', error.message);
      throw error;
    }
  }
}

module.exports = new EsinfaController();