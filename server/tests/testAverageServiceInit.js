const AverageService = require('../../services/averageService');

async function testAverageServiceInit() {
    console.log('🧪 Probando inicialización de AverageService...\n');
    
    try {
        console.log('1️⃣ Creando instancia de AverageService...');
        const averageService = new AverageService();
        console.log('✅ AverageService creado exitosamente');
        
        console.log('2️⃣ Verificando propiedades del servicio...');
        console.log('   - timezone:', averageService.timezone);
        console.log('   - logger:', typeof averageService.logger);
        console.log('   - readerPool:', typeof averageService.readerPool);
        console.log('   - writerPool:', typeof averageService.writerPool);
        
        if (!averageService.readerPool) {
            console.log('❌ readerPool es undefined');
            return;
        }
        
        if (!averageService.writerPool) {
            console.log('❌ writerPool es undefined');
            return;
        }
        
        console.log('3️⃣ Probando conexión a la base de datos...');
        const connection = await averageService.readerPool.getConnection();
        console.log('✅ Conexión a base de datos exitosa');
        
        // Probar una query simple
        const [rows] = await connection.query('SELECT 1 as test');
        console.log('✅ Query de prueba exitosa:', rows[0]);
        
        connection.release();
        console.log('✅ Conexión liberada');
        
        console.log('4️⃣ Probando obtención de configuración...');
        const configuracion = await averageService.obtenerConfiguracionVariables();
        console.log(`✅ Configuración obtenida: ${configuracion.length} variables`);
        
        if (configuracion.length > 0) {
            console.log('   Configuración:');
            configuracion.slice(0, 5).forEach(config => {
                console.log(`   - ${config.station_name}: ${config.variable_name}`);
            });
        }
        
        console.log('\n🎉 AverageService funciona correctamente!');
        
    } catch (error) {
        console.error('❌ Error en test:', error);
        console.error('Stack trace:', error.stack);
    }
}

// Ejecutar el test
testAverageServiceInit(); 