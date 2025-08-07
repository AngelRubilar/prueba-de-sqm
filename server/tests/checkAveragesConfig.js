const AverageService = require('../../services/averageService');

async function checkAveragesConfig() {
    console.log('🔍 Verificando configuración de promedios...\n');
    
    const averageService = new AverageService();
    
    try {
        // 1. Verificar configuración de variables
        console.log('1️⃣ Verificando configuración de variables...');
        const configuracion = await averageService.obtenerConfiguracionVariables();
        console.log(`✅ Configuración obtenida: ${configuracion.length} variables configuradas`);
        
        if (configuracion.length > 0) {
            console.log('   Configuración:');
            configuracion.forEach(config => {
                console.log(`   - ${config.station_name}: ${config.variable_name}`);
            });
        } else {
            console.log('   ⚠️ No hay configuración disponible');
        }

        // 2. Verificar tabla de configuración directamente
        console.log('\n2️⃣ Verificando tabla configuracion_variables_estacion...');
        const connection = await averageService.readerPool.getConnection();
        try {
            const query = `
                SELECT station_name, variable_name, activo, fecha_creacion
                FROM configuracion_variables_estacion
                ORDER BY station_name, variable_name
            `;
            
            const [rows] = await connection.query(query);
            console.log(`✅ Tabla configuracion_variables_estacion: ${rows.length} registros`);
            
            if (rows.length > 0) {
                console.log('   Registros en la tabla:');
                rows.forEach(row => {
                    console.log(`   - ${row.station_name} ${row.variable_name} (activo: ${row.activo})`);
                });
            } else {
                console.log('   ⚠️ La tabla está vacía');
            }
        } catch (error) {
            if (error.code === 'ER_NO_SUCH_TABLE') {
                console.log(`✅ Tabla configuracion_variables_estacion: No existe (usando configuración por defecto)`);
            } else {
                console.log(`❌ Error consultando tabla de configuración: ${error.message}`);
            }
        } finally {
            connection.release();
        }

        // 3. Verificar promedios más recientes
        console.log('\n3️⃣ Verificando promedios más recientes...');
        if (configuracion.length > 0) {
            const promedios = await averageService.obtenerPromediosMultiples(configuracion);
            console.log(`✅ Promedios obtenidos: ${promedios.length} registros`);
            
            if (promedios.length > 0) {
                console.log('   Últimos promedios:');
                promedios.slice(0, 5).forEach(promedio => {
                    console.log(`   - ${promedio.station_name} ${promedio.variable_name}:`);
                    console.log(`     Hora: ${promedio.promedio_hora}, Día: ${promedio.promedio_dia}`);
                    console.log(`     Fecha: ${promedio.fecha} ${promedio.hora_calculo}`);
                });
            } else {
                console.log('   ⚠️ No hay promedios calculados');
            }
        }

        // 4. Verificar tabla de promedios directamente
        console.log('\n4️⃣ Verificando tabla promedios_diarios...');
        const connection2 = await averageService.readerPool.getConnection();
        try {
            const query = `
                SELECT 
                    station_name, 
                    variable_name, 
                    COUNT(*) as total_registros,
                    MAX(fecha) as ultima_fecha,
                    MAX(hora_calculo) as ultima_hora
                FROM promedios_diarios
                GROUP BY station_name, variable_name
                ORDER BY station_name, variable_name
            `;
            
            const [rows] = await connection2.query(query);
            console.log(`✅ Tabla promedios_diarios: ${rows.length} combinaciones estación-variable`);
            
            if (rows.length > 0) {
                console.log('   Resumen por estación y variable:');
                rows.forEach(row => {
                    console.log(`   - ${row.station_name} ${row.variable_name}: ${row.total_registros} registros`);
                    console.log(`     Último: ${row.ultima_fecha} ${row.ultima_hora}`);
                });
            } else {
                console.log('   ⚠️ La tabla está vacía');
            }
        } finally {
            connection2.release();
        }
        
    } catch (error) {
        console.error('❌ Error en verificación:', error);
    }
}

// Ejecutar la verificación
checkAveragesConfig(); 