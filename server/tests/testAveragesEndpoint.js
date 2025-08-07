const AverageService = require('../../services/averageService');

async function testAveragesEndpoint() {
    console.log('🧪 Probando endpoint de promedios...\n');

    const averageService = new AverageService();

    try {
        // 1. Verificar configuración
        console.log('1. Verificando configuración...');
        const configuracion = await averageService.obtenerConfiguracionVariables();
        console.log(`✅ Configuración: ${configuracion.length} variables configuradas`);

        // 2. Ejecutar cálculo de promedios
        console.log('\n2. Calculando promedios...');
        await averageService.calcularPromediosDiarios();
        console.log('✅ Cálculo completado');

        // 3. Obtener últimos promedios
        console.log('\n3. Obteniendo últimos promedios...');
        const promedios = await averageService.obtenerPromediosMultiples(configuracion);
        console.log(`✅ Promedios obtenidos: ${promedios.length} registros`);

        // 4. Mostrar ejemplos
        if (promedios.length > 0) {
            console.log('\n📊 Ejemplos de promedios:');
            promedios.slice(0, 5).forEach(promedio => {
                console.log(`   ${promedio.station_name} ${promedio.variable_name}:`);
                console.log(`     - Última hora: ${promedio.promedio_hora || 'N/A'}`);
                console.log(`     - Diario (24h): ${promedio.promedio_dia || 'N/A'} (valor principal)`);
                console.log(`     - Calculado: ${promedio.fecha} ${promedio.hora_calculo}`);
                console.log(`     - Creado: ${promedio.fecha_creacion}`);
            });
        }

        console.log('\n🎉 Endpoint listo para usar!');
        console.log('\n📝 Para probar:');
        console.log('   curl http://localhost:3000/api/promedios');
        console.log('   curl http://localhost:3000/api/promedios/estacion/E1');

    } catch (error) {
        console.error('❌ Error:', error);
        throw error;
    }
}

// Ejecutar si se llama directamente
if (require.main === module) {
    testAveragesEndpoint()
        .then(() => {
            console.log('\n✅ Prueba completada');
            process.exit(0);
        })
        .catch((error) => {
            console.error('\n❌ Error en prueba:', error);
            process.exit(1);
        });
}

module.exports = { testAveragesEndpoint }; 