const mysql = require('mysql2/promise');
const moment = require('moment-timezone');
const { writerPool } = require('./config/database');

// Configuración
const CONFIG = {
  diasAntiguedad: 7,
  zonaHoraria: 'America/Santiago',
  limiteRegistros: 30000,
  intervaloEjecucion: 3 * 60 * 1000 // 3 minutos en milisegundos
};

// Mapeo de estaciones a tablas
const ESTACIONES_TABLAS = {
  serpram: ['E1', 'E2', 'E3', 'E4'],
  esinfa: ['E5'],
  ayt: ['E6', 'E7', 'E8'],
  sercoamb: ['E9', 'E10'],
  geovalidata: ['E11', 'E12', 'E13', 'E14', 'E15', 'Desconocido']
};

async function moverDatosHistoricos() {
  let connection;

  try {
    connection = await writerPool.getConnection();
    await connection.beginTransaction();

    // Calcular la fecha límite (7 días atrás desde ahora)
    const fechaLimite = moment()
      .tz(CONFIG.zonaHoraria)
      .subtract(CONFIG.diasAntiguedad, 'days')
      .format('YYYY-MM-DD HH:mm:ss');
    
    console.log('Procesando datos anteriores a:', fechaLimite);

    // 1. Seleccionar los datos antiguos (más de 7 días) con límite
    const [rows] = await connection.query(
      `SELECT timestamp, station_name, variable_name, valor 
       FROM datos 
       WHERE timestamp < ? 
       ORDER BY timestamp ASC 
       LIMIT ?`,
      [fechaLimite, CONFIG.limiteRegistros]
    );

    if (rows.length === 0) {
      console.log('No hay datos antiguos para mover.');
      return;
    }

    console.log(`Procesando ${rows.length} registros antiguos...`);

    // 2. Agrupar datos por tabla destino
    const datosPorTabla = {};
    for (const tabla in ESTACIONES_TABLAS) {
      datosPorTabla[tabla] = [];
    }

    // 3. Clasificar los datos según la estación
    for (const row of rows) {
      for (const [tabla, estaciones] of Object.entries(ESTACIONES_TABLAS)) {
        if (estaciones.includes(row.station_name)) {
          datosPorTabla[tabla].push([
            row.timestamp,
            row.station_name,
            row.variable_name,
            row.valor
          ]);
          break;
        }
      }
    }

    // 4. Insertar los datos en sus respectivas tablas
    for (const [tabla, datos] of Object.entries(datosPorTabla)) {
      if (datos.length > 0) {
        const query = `INSERT INTO ${tabla} (timestamp, station_name, variable_name, valor) VALUES ?`;
        await connection.query(query, [datos]);
        console.log(`Insertados ${datos.length} registros en ${tabla}`);
      }
    }

    // 5. Eliminar los datos procesados de la tabla original
    const [deleteResult] = await connection.query(
      'DELETE FROM datos WHERE timestamp < ? LIMIT ?',
      [fechaLimite, CONFIG.limiteRegistros]
    );
    console.log(`Eliminados ${deleteResult.affectedRows} registros de la tabla datos`);

    // 6. Verificar y limpiar datos adicionales si es necesario
    const [countResult] = await connection.query(
      'SELECT COUNT(*) as total FROM datos WHERE timestamp < ?',
      [fechaLimite]
    );
    
    if (countResult[0].total > 0) {
      console.log(`Aún quedan ${countResult[0].total} registros antiguos por procesar`);
    }

    await connection.commit();
    console.log('=== Proceso de traspaso completado exitosamente ===');

  } catch (error) {
    if (connection) {
      await connection.rollback();
    }
    console.error('Error en el proceso de traspaso:', {
      mensaje: error.message,
      stack: error.stack,
      codigo: error.code
    });
  } finally {
    if (connection) {
      connection.release();
    }
  }
}

// Función para iniciar el proceso programado
function iniciarProcesoProgramado() {
  console.log('Iniciando proceso programado de traspaso de datos...');
  console.log(`Configuración:
    - Días de antigüedad: ${CONFIG.diasAntiguedad}
    - Límite por ejecución: ${CONFIG.limiteRegistros} registros
    - Intervalo de ejecución: ${CONFIG.intervaloEjecucion/1000/60} minutos
  `);
  
  // Ejecutar inmediatamente la primera vez
  moverDatosHistoricos();
  
  // Programar la ejecución cada 3 minutos
  setInterval(moverDatosHistoricos, CONFIG.intervaloEjecucion);
}

module.exports = { 
  moverDatosHistoricos,
  iniciarProcesoProgramado
};