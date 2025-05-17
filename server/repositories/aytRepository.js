const { writerPool } = require('../config/database');

class AytRepository {
    async guardarDatos(datosArray) {
        if (!datosArray || datosArray.length === 0) {
            console.log('No hay datos para guardar');
            return false;
        }

        const connection = await writerPool.getConnection();
        try {
            const valores = datosArray.map(([timestamp, station_name, variable_name, valor]) => 
                [timestamp, station_name, variable_name, valor]
            );

            // Consulta modificada especificando el collation
            const queryInsertNoDuplicates = `
                INSERT INTO datos (timestamp, station_name, variable_name, valor)
                SELECT DISTINCT t.timestamp, t.station_name, t.variable_name, t.valor
                FROM (
                    SELECT 
                        timestamp_data as timestamp,
                        station_name_data as station_name,
                        variable_name_data as variable_name,
                        CAST(valor_data AS DECIMAL(10,2)) as valor
                    FROM JSON_TABLE(
                        ?,
                        '$[*]' COLUMNS (
                            timestamp_data VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci PATH '$[0]',
                            station_name_data VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci PATH '$[1]',
                            variable_name_data VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci PATH '$[2]',
                            valor_data VARCHAR(20) PATH '$[3]'
                        )
                    ) AS datos
                ) AS t
                WHERE NOT EXISTS (
                    SELECT 1 FROM datos d
                    WHERE d.timestamp = t.timestamp COLLATE utf8mb4_unicode_ci
                    AND d.station_name = t.station_name COLLATE utf8mb4_unicode_ci
                    AND d.variable_name = t.variable_name COLLATE utf8mb4_unicode_ci
                )`;

            const [result] = await connection.query(
                queryInsertNoDuplicates, 
                [JSON.stringify(valores)]
            );

            console.log(`=== Procesamiento de datos AYT completado ===`);
            console.log(`Registros procesados: ${datosArray.length}`);
            console.log(`Nuevos registros insertados: ${result.affectedRows}`);
            
            return result.affectedRows > 0;

        } catch (error) {
            console.error('Error al guardar los datos en MySQL:', error.message);
            throw error;
        } finally {
            connection.release();
        }
    }
}

module.exports = new AytRepository();