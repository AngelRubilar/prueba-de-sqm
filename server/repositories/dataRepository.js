const { writerPool } = require('../config/database');
const moment = require('moment-timezone');

class DataRepository {
  constructor() {
    // this.pool = pool; // No longer need this.pool if using writerPool directly
  }

  async guardarDatosEnMySQL(datosArray) {
    const connection = await writerPool.getConnection();
    const queryInsert = 'INSERT INTO datos (timestamp, station_name, variable_name, valor) VALUES ?';
    const queryCheck = `
      SELECT CONCAT(timestamp, '-', station_name, '-', variable_name) AS clave
      FROM datos
      WHERE CONCAT(timestamp, '-', station_name, '-', variable_name) IN (?);`;

    const datosParaInsertar = [];

    // Crear un conjunto de claves únicas para verificar la existencia
    const claves = datosArray.map(([timestamp, station_name, variable_name]) => 
      `${timestamp}-${station_name}-${variable_name}`
    );

    if (claves.length === 0) {
      console.log('No hay claves para verificar.');
      connection.release();
      return;
    }

    try {
      console.log('\n=== INICIANDO PROCESO DE GUARDADO ===');
      console.log(`Total de registros a procesar: ${datosArray.length}`);

      // Verificar la existencia de los registros en una sola consulta
      const [rows] = await connection.query(queryCheck, [claves]);
      //console.log(`Registros existentes encontrados: ${rows.length}`);

      // Crear un conjunto de claves existentes para una búsqueda rápida
      const clavesExistentes = new Set(rows.map(row => row.clave));

      // Contador para el resumen
      const resumenPorEstacion = {};

      for (const datos of datosArray) {
        if (!Array.isArray(datos)) {
          continue;
        }
        const [timestamp, station_name, variable_name, valor] = datos;
        const clave = `${timestamp}-${station_name}-${variable_name}`;
        
        if (!clavesExistentes.has(clave)) {
          datosParaInsertar.push(datos);
          
          // Actualizar resumen
          if (!resumenPorEstacion[station_name]) {
            resumenPorEstacion[station_name] = {
              total: 0,
              variables: new Set()
            };
          }
          resumenPorEstacion[station_name].total++;
          resumenPorEstacion[station_name].variables.add(variable_name);
        } else {
          //console.log(`El registro ya existe: ${clave}`);
        }
      }

      console.log('\n=== RESUMEN DE DATOS A INSERTAR ===');
      console.log(`Registros duplicados encontrados: ${datosArray.length - datosParaInsertar.length}`);
      console.log(`Registros nuevos a insertar: ${datosParaInsertar.length}`);
      
      // Mostrar resumen por estación
      console.log('\nDesglose por estación:');
      Object.entries(resumenPorEstacion).forEach(([estacion, datos]) => {
        console.log(`\nEstación: ${estacion}`);
        console.log(`- Total de registros: ${datos.total}`);
        console.log(`- Variables diferentes: ${datos.variables.size}`);
        console.log(`- Variables: ${Array.from(datos.variables).join(', ')}`);
      });

      if (datosParaInsertar.length > 0) {
        console.log('\nEjecutando query de inserción...');
        await connection.query(queryInsert, [datosParaInsertar]);
        
        console.log('\n=== RESUMEN FINAL ===');
        console.log(`Total de registros insertados: ${datosParaInsertar.length}`);
        console.log(`Fecha y hora de inserción: ${moment().format('YYYY-MM-DD HH:mm:ss')}`);
        console.log('=== PROCESO COMPLETADO ===\n');
      } else {
        console.log('\nNo hay datos nuevos para insertar en la base de datos.');
      }
    } catch (error) {
      console.error('\nError al guardar los datos en MySQL:', error.message);
      if (error.sqlMessage) {
        console.error('Error SQL:', error.sqlMessage);
      }
      throw error;
    } finally {
      connection.release();
    }
  }
}

module.exports = new DataRepository();