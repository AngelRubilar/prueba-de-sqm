const axios = require('axios');

async function testHospitalData() {
    try {
        console.log('=== PRUEBA DE DATOS HOSPITAL ===');
        
        // Probar el endpoint de viento hospital
        const response = await axios.get('http://localhost:3000/api/datos-viento-hospital');
        
        console.log('✅ Endpoint funciona');
        console.log('Datos recibidos:', response.data.length, 'registros');
        
        if (response.data.length > 0) {
            console.log('\n📊 Primer registro:');
            console.log(JSON.stringify(response.data[0], null, 2));
            
            console.log('\n📊 Último registro:');
            console.log(JSON.stringify(response.data[response.data.length - 1], null, 2));
            
            // Verificar estructura
            const sample = response.data[0];
            console.log('\n🔍 Estructura del registro:');
            console.log('- timestamp:', sample.timestamp, '(', typeof sample.timestamp, ')');
            console.log('- station_name:', sample.station_name, '(', typeof sample.station_name, ')');
            console.log('- velocidad:', sample.velocidad, '(', typeof sample.velocidad, ')');
            console.log('- direccion:', sample.direccion, '(', typeof sample.direccion, ')');
            
            // Verificar si hay datos válidos
            const validData = response.data.filter(d => 
                d.velocidad !== null && d.velocidad !== undefined && 
                d.direccion !== null && d.direccion !== undefined
            );
            
            console.log('\n✅ Datos válidos (con velocidad y dirección):', validData.length, 'registros');
            
            if (validData.length > 0) {
                console.log('📊 Primer dato válido:');
                console.log(JSON.stringify(validData[0], null, 2));
            }
        } else {
            console.log('⚠️ No hay datos disponibles');
        }
        
    } catch (error) {
        console.error('❌ Error:', error.message);
        if (error.response) {
            console.error('Status:', error.response.status);
            console.error('Data:', error.response.data);
        }
    }
}

testHospitalData(); 