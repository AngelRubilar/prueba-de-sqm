const AverageController = require('../../controllers/averageController');

async function testEndpointSimple() {
    console.log('🧪 Probando endpoint simple...\n');
    
    const controller = new AverageController();
    
    try {
        // Simular request y response
        const req = {};
        const res = {
            json: (data) => {
                console.log('✅ Respuesta del endpoint:');
                console.log('   Success:', data.success);
                console.log('   Timestamp:', data.timestamp);
                console.log('   Data keys:', Object.keys(data.data));
                
                if (data.success && data.data) {
                    Object.keys(data.data).forEach(station => {
                        console.log(`\n📊 Estación ${station}:`);
                        const stationData = data.data[station];
                        Object.keys(stationData).forEach(variable => {
                            const varData = stationData[variable];
                            console.log(`   ${variable}:`);
                            console.log(`     - valor: ${varData.valor}`);
                            console.log(`     - promedioHora: ${varData.promedioHora}`);
                            console.log(`     - promedioDiario: ${varData.promedioDiario}`);
                        });
                    });
                }
            },
            status: (code) => {
                console.log(`❌ Status code: ${code}`);
                return {
                    json: (data) => {
                        console.log('❌ Error response:', data);
                    }
                };
            }
        };
        
        await controller.obtenerUltimosPromedios(req, res);
        
    } catch (error) {
        console.error('❌ Error en test:', error);
    }
}

// Ejecutar el test
testEndpointSimple(); 