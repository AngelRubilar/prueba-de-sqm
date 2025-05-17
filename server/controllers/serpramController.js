const serpramService = require('../services/serpramService');
const dataRepository = require('../repositories/dataRepository');

class SerpramController {
  /**
   * Ejecuta la consulta de Serpram para cada dispositivo,
   * guarda los datos válidos en MySQL y devuelve un array
   * con TODOS los registros efectivamente procesados.
   */
  async realizarConsulta({ since }) {
    // 1) Preparo un array donde acumularé todos los registros insertados
    const allDatos = [];

    try {
      for (const dispositivo of serpramService.dispositivos) {
        try {
          console.log(`Procesando dispositivo Serpram: ${dispositivo}`);
          const datosArray = await serpramService.consultarAPI(dispositivo);

          // Verificar que datosArray sea un array válido
          if (!Array.isArray(datosArray) || datosArray.length === 0) {
            console.log(`No hay datos para guardar del dispositivo ${dispositivo}`);
            continue;
          }

          // Filtrar solo los datos con estructura correcta
          const datosValidos = datosArray.filter(dato => {
            if (!Array.isArray(dato) || dato.length !== 4) {
              console.error(`Dato inválido para ${dispositivo}:`, dato);
              return false;
            }
            const [timestamp, station_name, variable_name, valor] = dato;
            if (!timestamp || !station_name || !variable_name || valor === undefined) {
              console.error(`Dato incompleto para ${dispositivo}:`, dato);
              return false;
            }
            return true;
          });

          if (datosValidos.length > 0) {
            console.log(`Guardando ${datosValidos.length} registros para ${dispositivo}`);
            // Guardar en MySQL
            await dataRepository.guardarDatosEnMySQL(datosValidos);
            // 2) Agrego al array general los registros que acabo de guardar
            allDatos.push(...datosValidos);
            console.log(`Datos guardados exitosamente para ${dispositivo}`);
          } else {
            console.log(`No hay datos válidos para guardar del dispositivo ${dispositivo}`);
          }
        } catch (error) {
          console.error(`Error al procesar dispositivo ${dispositivo}:`, error.message);
        }
      }

      // 3) Devuelvo TODOS los datos que efectivamente procesé/inserte
      return allDatos;
    } catch (error) {
      console.error('Error en la consulta general:', error.message);
      throw error;
    }
  }
}

module.exports = new SerpramController();