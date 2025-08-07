const AverageService = require('../../services/averageService');

async function debugAverages() {
    console.log('🔍 DEBUG: Cálculo de promedios\n');
    
    const averageService = new AverageService();
    
    try {
        // Probar cálculo de promedio por hora
        console.log('1️⃣ Probando promedio por hora para E1 PM10...');
        const promedioHora = await averageService.calcularPromedioHora('E1', 'PM10');
        console.log(`Resultado promedio hora: ${promedioHora} (tipo: ${typeof promedioHora})`);
        
        // Probar cálculo de promedio diario
        console.log('\n2️⃣ Probando promedio diario para E1 PM10...');
        const promedioDiario = await averageService.calcularPromedioDia('E1', 'PM10', '2025-07-28');
        console.log(`Resultado promedio diario: ${promedioDiario} (tipo: ${typeof promedioDiario})`);
        
        // Probar query directa para comparar
        console.log('\n3️⃣ Probando query directa...');
        const connection = await averageService.readerPool.getConnection();
        try {
            const query = `
                SELECT 
                    AVG(valor) as promedio,
                    COUNT(*) as cantidad
                FROM datos 
                WHERE station_name = 'E1' 
                AND variable_name = 'PM10' 
                AND timestamp >= '2025-07-28 01:13:40'
                AND timestamp <= '2025-07-28 02:13:40'
                AND valor IS NOT NULL
            `;
            
            const [rows] = await connection.query(query);
            console.log(`Query directa - promedio: ${rows[0]?.promedio} (tipo: ${typeof rows[0]?.promedio})`);
            console.log(`Query directa - cantidad: ${rows[0]?.cantidad}`);
        } finally {
            connection.release();
        }
        
    } catch (error) {
        console.error('❌ Error en debug:', error);
    }
}

// Ejecutar si se llama directamente
if (require.main === module) {
    debugAverages()
        .then(() => {
            console.log('\n✅ Debug completado');
            process.exit(0);
        })
        .catch((error) => {
            console.error('\n❌ Error en debug:', error);
            process.exit(1);
        });
}

module.exports = { debugAverages }; 