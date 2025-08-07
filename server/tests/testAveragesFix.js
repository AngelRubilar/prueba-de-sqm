const AverageService = require('../../services/averageService');

async function testAveragesFix() {
    console.log('🧪 Probando fix de promedios diarios...\n');
    
    const averageService = new AverageService();
    
    try {
        // Probar cálculo de promedio por hora
        console.log('1️⃣ Probando promedio por hora para E10 PM2_5...');
        const promedioHora = await averageService.calcularPromedioHora('E10', 'PM2_5');
        console.log(`✅ Promedio hora: ${promedioHora} (tipo: ${typeof promedioHora})`);
        
        // Probar cálculo de promedio diario
        console.log('\n2️⃣ Probando promedio diario para E10 PM2_5...');
        const promedioDiario = await averageService.calcularPromedioDia('E10', 'PM2_5', '2025-07-28');
        console.log(`✅ Promedio diario: ${promedioDiario} (tipo: ${typeof promedioDiario})`);
        
        // Probar cálculo completo
        console.log('\n3️⃣ Probando cálculo completo de promedios...');
        await averageService.calcularPromediosDiarios();
        console.log('✅ Cálculo completo ejecutado');
        
        // Verificar en base de datos
        console.log('\n4️⃣ Verificando datos en base de datos...');
        const connection = await averageService.readerPool.getConnection();
        try {
            const query = `
                SELECT 
                    station_name,
                    variable_name,
                    promedio_hora,
                    promedio_dia,
                    fecha,
                    hora_calculo
                FROM promedios_diarios 
                WHERE fecha = '2025-07-28'
                AND station_name = 'E10'
                AND variable_name = 'PM2_5'
                ORDER BY hora_calculo DESC
                LIMIT 5
            `;
            
            const [rows] = await connection.query(query);
            console.log(`✅ Registros encontrados: ${rows.length}`);
            
            if (rows.length > 0) {
                console.log('   Últimos registros:');
                rows.forEach((row, index) => {
                    console.log(`   ${index + 1}. ${row.station_name} ${row.variable_name}:`);
                    console.log(`      - Hora: ${row.promedio_hora} (${typeof row.promedio_hora})`);
                    console.log(`      - Día: ${row.promedio_dia} (${typeof row.promedio_dia})`);
                    console.log(`      - Calculado: ${row.fecha} ${row.hora_calculo}`);
                });
            }
        } finally {
            connection.release();
        }
        
        console.log('\n🎉 Test completado!');
        
    } catch (error) {
        console.error('❌ Error en test:', error);
    }
}

// Ejecutar el test
testAveragesFix(); 