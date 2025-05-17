const dataRepository = require('./dataRepository');

class EsinfaRepository {
  constructor() {
    this.dataRepository = dataRepository;
  }

  async guardarDatosEsinfa(datosArray) {
    try {
      console.log('=== Iniciando guardado de datos Esinfa ===');
      console.log(`Cantidad de registros a procesar: ${datosArray.length}`);

      if (datosArray.length === 0) {
        console.log('No hay datos de Esinfa para guardar.');
        return;
      }

      // Validar la estructura de los datos
      const datosValidos = datosArray.filter(dato => {
        if (!Array.isArray(dato) || dato.length !== 4) {
          console.error('Dato inválido:', dato);
          return false;
        }
        const [timestamp, station_name, variable_name, valor] = dato;
        if (!timestamp || !station_name || !variable_name || valor === undefined) {
          console.error('Dato incompleto:', dato);
          return false;
        }
        return true;
      });

      console.log(`Datos válidos para guardar: ${datosValidos.length}`);

      // Guardar los datos usando el método del repositorio base
      await this.dataRepository.guardarDatosEnMySQL(datosValidos);

      console.log('=== Datos de Esinfa guardados exitosamente ===');
    } catch (error) {
      console.error('Error al guardar datos de Esinfa:', error.message);
      throw error;
    }
  }
}

// Exportar una instancia de la clase
module.exports = new EsinfaRepository();