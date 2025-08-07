const axios = require('axios');

async function testAveragesAPI() {
    console.log('🧪 Probando endpoint de promedios...\n');
    
    const baseURL = 'http://localhost:3000/api';
    
    try {
        // 1. Probar endpoint principal de promedios
        console.log('1️⃣ Probando GET /promedios...');
        const response = await axios.get(`${baseURL}/promedios`);
        
        console.log('✅ Status:', response.status);
        console.log('✅ Headers:', response.headers['content-type']);
        console.log('✅ Data structure:', Object.keys(response.data));
        
        if (response.data.success) {
            console.log('✅ Success:', response.data.success);
            console.log('✅ Timestamp:', response.data.timestamp);
            
            const data = response.data.data;
            console.log('✅ Estaciones disponibles:', Object.keys(data));
            
            // Mostrar algunos ejemplos de datos
            Object.keys(data).slice(0, 3).forEach(station => {
                console.log(`\n📊 Estación ${station}:`);
                const stationData = data[station];
                Object.keys(stationData).forEach(variable => {
                    const varData = stationData[variable];
                    console.log(`   ${variable}:`);
                    console.log(`     - valor: ${varData.valor}`);
                    console.log(`     - promedioHora: ${varData.promedioHora}`);
                    console.log(`     - promedioDiario: ${varData.promedioDiario}`);
                    console.log(`     - fechaCreacion: ${varData.fechaCreacion}`);
                    console.log(`     - horaCalculo: ${varData.horaCalculo}`);
                });
            });
        } else {
            console.log('❌ Error en respuesta:', response.data);
        }
        
        // 2. Probar endpoint de estación específica
        console.log('\n2️⃣ Probando GET /promedios/estacion/E1...');
        const stationResponse = await axios.get(`${baseURL}/promedios/estacion/E1`);
        
        console.log('✅ Status:', stationResponse.status);
        if (stationResponse.data.success) {
            console.log('✅ Estación E1 data:', stationResponse.data.data.length, 'registros');
            if (stationResponse.data.data.length > 0) {
                console.log('✅ Primer registro:', stationResponse.data.data[0]);
            }
        } else {
            console.log('❌ Error en respuesta de estación:', stationResponse.data);
        }
        
    } catch (error) {
        console.error('❌ Error en test:', error.message);
        if (error.response) {
            console.error('❌ Response status:', error.response.status);
            console.error('❌ Response data:', error.response.data);
        }
    }
}

// Ejecutar el test
testAveragesAPI(); 