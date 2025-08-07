const AverageService = require('../../services/averageService');
const AverageController = require('../../controllers/averageController');

async function testCompleteSystem() {
    console.log('🧪 PRUEBA COMPLETA DEL SISTEMA DE PROMEDIOS\n');
    console.log('=' .repeat(60));

    const averageService = new AverageService();
    const averageController = new AverageController();

    try {
        // 1. Verificar configuración
        console.log('\n1️⃣ Verificando configuración de variables...');
        const configuracion = await averageService.obtenerConfiguracionVariables();
        console.log(`✅ Configuración: ${configuracion.length} variables configuradas`);
        
        // Mostrar configuración
        const configByStation = {};
        configuracion.forEach(config => {
            if (!configByStation[config.station_name]) {
                configByStation[config.station_name] = [];
            }
            configByStation[config.station_name].push(config.variable_name);
        });
        
        Object.keys(configByStation).forEach(station => {
            console.log(`   ${station}: ${configByStation[station].join(', ')}`);
        });

        // 2. Verificar datos existentes
        console.log('\n2️⃣ Verificando datos existentes...');
        const connection = await averageService.readerPool.getConnection();
        try {
            const [dataCount] = await connection.query('SELECT COUNT(*) as total FROM datos');
            console.log(`✅ Datos en tabla 'datos': ${dataCount[0].total} registros`);
            
            if (dataCount[0].total > 0) {
                const [sampleData] = await connection.query(`
                    SELECT station_name, variable_name, COUNT(*) as count, 
                           MIN(timestamp) as min_time, MAX(timestamp) as max_time
                    FROM datos 
                    GROUP BY station_name, variable_name 
                    ORDER BY station_name, variable_name 
                    LIMIT 5
                `);
                console.log('📊 Muestra de datos disponibles:');
                sampleData.forEach(row => {
                    console.log(`   ${row.station_name} ${row.variable_name}: ${row.count} registros (${row.min_time} a ${row.max_time})`);
                });
            }
        } finally {
            connection.release();
        }

        // 3. Ejecutar cálculo de promedios
        console.log('\n3️⃣ Calculando promedios...');
        await averageService.calcularPromediosDiarios();
        console.log('✅ Cálculo completado');

        // 4. Verificar promedios calculados
        console.log('\n4️⃣ Verificando promedios calculados...');
        const promedios = await averageService.obtenerPromediosMultiples(configuracion);
        console.log(`✅ Promedios calculados: ${promedios.length} registros`);

        // 5. Probar API endpoints
        console.log('\n5️⃣ Probando endpoints de API...');
        
        // Crear mock de request y response para probar el controlador
        const mockReq = {};
        const mockRes = {
            json: function(data) {
                this.data = data;
                return this;
            },
            status: function(code) {
                this.statusCode = code;
                return this;
            }
        };
        
        // Probar endpoint principal
        await averageController.obtenerUltimosPromedios(mockReq, mockRes);
        const response = mockRes.data;
        
        if (response && response.success) {
            console.log(`✅ Endpoint /api/promedios: OK`);
            if (response.data) {
                const stations = Object.keys(response.data);
                console.log(`   Estaciones con datos: ${stations.length}`);
                stations.slice(0, 3).forEach(station => {
                    const variables = Object.keys(response.data[station]);
                    console.log(`   ${station}: ${variables.join(', ')}`);
                });
            }
        } else {
            console.log(`❌ Endpoint /api/promedios: ERROR`);
        }

        // 6. Mostrar ejemplos detallados
        console.log('\n6️⃣ Ejemplos de promedios calculados:');
        if (promedios.length > 0) {
            // Agrupar por estación para mejor visualización
            const promediosPorEstacion = {};
            promedios.forEach(promedio => {
                if (!promediosPorEstacion[promedio.station_name]) {
                    promediosPorEstacion[promedio.station_name] = [];
                }
                promediosPorEstacion[promedio.station_name].push(promedio);
            });

            Object.keys(promediosPorEstacion).slice(0, 3).forEach(station => {
                console.log(`\n   📍 Estación ${station}:`);
                promediosPorEstacion[station].forEach(promedio => {
                    console.log(`      ${promedio.variable_name}:`);
                    
                    // Manejar promedio_hora
                    let promedioHoraStr = 'N/A';
                    if (promedio.promedio_hora !== null && promedio.promedio_hora !== undefined) {
                        const promedioHora = parseFloat(promedio.promedio_hora);
                        if (!isNaN(promedioHora)) {
                            promedioHoraStr = promedioHora.toFixed(2);
                        }
                    }
                    
                    // Manejar promedio_dia
                    let promedioDiaStr = 'N/A';
                    if (promedio.promedio_dia !== null && promedio.promedio_dia !== undefined) {
                        const promedioDia = parseFloat(promedio.promedio_dia);
                        if (!isNaN(promedioDia)) {
                            promedioDiaStr = promedioDia.toFixed(2);
                        }
                    }
                    
                    console.log(`        - Última hora: ${promedioHoraStr}`);
                    console.log(`        - Diario (24h): ${promedioDiaStr} ← VALOR PRINCIPAL`);
                    console.log(`        - Calculado: ${promedio.fecha} ${promedio.hora_calculo}`);
                    console.log(`        - Registros: ${promedio.cantidad_registros || 0}`);
                });
            });
        }

        // 7. Verificar estructura de respuesta API
        console.log('\n7️⃣ Estructura de respuesta API:');
        if (response && response.success && response.data) {
            const firstStation = Object.keys(response.data)[0];
            if (firstStation) {
                const firstVariable = Object.keys(response.data[firstStation])[0];
                if (firstVariable) {
                    const example = response.data[firstStation][firstVariable];
                    console.log(`   Ejemplo para ${firstStation}.${firstVariable}:`);
                    console.log(`   {
     "valor": ${example.valor !== null ? example.valor : 'null'},              // ← Valor principal (promedio diario)
     "promedioHora": ${example.promedioHora !== null ? example.promedioHora : 'null'}, // Promedio última hora
     "promedioDiario": ${example.promedioDiario !== null ? example.promedioDiario : 'null'}, // Promedio diario
     "fechaCreacion": "${example.fechaCreacion}",
     "horaCalculo": "${example.horaCalculo}"
   }`);
                }
            }
        }

        console.log('\n' + '=' .repeat(60));
        console.log('🎉 SISTEMA DE PROMEDIOS FUNCIONANDO CORRECTAMENTE!');
        console.log('\n📝 Para usar en el frontend:');
        console.log('   const response = await fetch("/api/promedios");');
        console.log('   const so2Value = response.data.E1?.SO2?.valor; // Promedio diario');
        console.log('\n📝 Para probar manualmente:');
        console.log('   curl http://localhost:3000/api/promedios');
        console.log('   curl http://localhost:3000/api/promedios/estacion/E1');

    } catch (error) {
        console.error('\n❌ Error en la prueba:', error);
        console.error('\n🔍 Detalles del error:');
        console.error('   Mensaje:', error.message);
        console.error('   Stack:', error.stack);
        throw error;
    }
}

// Ejecutar si se llama directamente
if (require.main === module) {
    testCompleteSystem()
        .then(() => {
            console.log('\n✅ Prueba completada exitosamente');
            process.exit(0);
        })
        .catch((error) => {
            console.error('\n❌ Error en la prueba:', error);
            process.exit(1);
        });
}

module.exports = { testCompleteSystem }; 