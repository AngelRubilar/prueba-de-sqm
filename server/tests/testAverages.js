const AverageService = require('../../services/averageService');
const AverageScheduler = require('../../services/averageScheduler');

async function testAveragesSystem() {
    console.log('🧪 Iniciando pruebas del sistema de promedios...\n');

    const averageService = new AverageService();
    const scheduler = new AverageScheduler();

    try {
        // 1. Probar obtención de configuración
        console.log('1. Probando obtención de configuración...');
        const configuracion = await averageService.obtenerConfiguracionVariables();
        console.log(`✅ Configuración obtenida: ${configuracion.length} variables configuradas`);
        configuracion.forEach(config => {
            console.log(`   - ${config.station_name}: ${config.variable_name}`);
        });

        // 2. Probar cálculo de promedios
        console.log('\n2. Probando cálculo de promedios...');
        await averageService.calcularPromediosDiarios();
        console.log('✅ Cálculo de promedios completado');

        // 3. Probar obtención de últimos promedios
        console.log('\n3. Probando obtención de últimos promedios...');
        const promedios = await averageService.obtenerPromediosMultiples(configuracion);
        console.log(`✅ Últimos promedios obtenidos: ${promedios.length} registros`);
        
        if (promedios.length > 0) {
            console.log('   Ejemplos de promedios:');
            promedios.slice(0, 3).forEach(promedio => {
                console.log(`   - ${promedio.station_name} ${promedio.variable_name}: Hora=${promedio.promedio_hora}, Día=${promedio.promedio_dia}`);
            });
        }

        // 4. Probar obtención de promedios históricos
        console.log('\n4. Probando obtención de promedios históricos...');
        if (configuracion.length > 0) {
            const primeraConfig = configuracion[0];
            const historicos = await averageService.obtenerPromediosHistoricos(
                primeraConfig.station_name,
                primeraConfig.variable_name,
                '2024-01-01',
                '2024-12-31'
            );
            console.log(`✅ Históricos obtenidos para ${primeraConfig.station_name} ${primeraConfig.variable_name}: ${historicos.length} registros`);
        }

        // 5. Probar scheduler
        console.log('\n5. Probando scheduler...');
        const estadoInicial = scheduler.getStatus();
        console.log(`✅ Estado inicial del scheduler: ${estadoInicial.isRunning ? 'Ejecutándose' : 'Detenido'}`);

        // 6. Probar cálculo manual
        console.log('\n6. Probando cálculo manual...');
        await scheduler.ejecutarCalculoManual();
        console.log('✅ Cálculo manual completado');

        // 7. Probar limpieza de datos antiguos
        console.log('\n7. Probando limpieza de datos antiguos...');
        await averageService.limpiarPromediosAntiguos();
        console.log('✅ Limpieza completada');

        console.log('\n🎉 Todas las pruebas completadas exitosamente!');

    } catch (error) {
        console.error('❌ Error en las pruebas:', error);
        throw error;
    }
}

// Ejecutar si se llama directamente
if (require.main === module) {
    testAveragesSystem()
        .then(() => {
            console.log('\n✅ Pruebas completadas');
            process.exit(0);
        })
        .catch((error) => {
            console.error('\n❌ Error en las pruebas:', error);
            process.exit(1);
        });
}

module.exports = { testAveragesSystem }; 