require('dotenv').config();

async function diagnoseAverages() {
    console.log('🔍 DIAGNÓSTICO DEL SISTEMA DE PROMEDIOS\n');
    console.log('=' .repeat(60));

    try {
        // 1. Verificar variables de entorno
        console.log('1️⃣ Verificando variables de entorno...');
        const requiredEnvVars = [
            'DB_HOST',
            'DB_WRITER_USER', 
            'DB_WRITER_PASSWORD',
            'DB_READER_USER',
            'DB_READER_PASSWORD',
            'DB_NAME'
        ];

        requiredEnvVars.forEach(varName => {
            const value = process.env[varName];
            if (value) {
                console.log(`   ✅ ${varName}: ${varName.includes('PASSWORD') ? '***' : value}`);
            } else {
                console.log(`   ❌ ${varName}: NO DEFINIDA`);
            }
        });

        // 2. Verificar configuración de base de datos
        console.log('\n2️⃣ Verificando configuración de base de datos...');
        const { writerPool, readerPool } = require('../config/database');
        
        if (writerPool) {
            console.log('   ✅ Writer pool creado');
        } else {
            console.log('   ❌ Writer pool NO creado');
        }
        
        if (readerPool) {
            console.log('   ✅ Reader pool creado');
        } else {
            console.log('   ❌ Reader pool NO creado');
        }

        // 3. Probar conexión a la base de datos
        console.log('\n3️⃣ Probando conexión a la base de datos...');
        if (readerPool) {
            try {
                const connection = await readerPool.getConnection();
                console.log('   ✅ Conexión a base de datos exitosa');
                
                // Verificar tablas
                const [tables] = await connection.query('SHOW TABLES');
                console.log(`   📊 Tablas disponibles: ${tables.length}`);
                tables.forEach(table => {
                    const tableName = Object.values(table)[0];
                    console.log(`      - ${tableName}`);
                });
                
                // Verificar tabla de configuración
                try {
                    const [configRows] = await connection.query('SELECT COUNT(*) as count FROM configuracion_variables_estacion');
                    console.log(`   📋 Configuraciones: ${configRows[0].count} registros`);
                } catch (error) {
                    if (error.code === 'ER_NO_SUCH_TABLE') {
                        console.log(`   📋 Configuraciones: Tabla no existe (usando configuración por defecto)`);
                    } else {
                        console.log(`   ❌ Error consultando configuración: ${error.message}`);
                    }
                }
                
                // Verificar tabla de datos
                const [dataRows] = await connection.query('SELECT COUNT(*) as count FROM datos');
                console.log(`   📊 Datos: ${dataRows[0].count} registros`);
                
                // Verificar tabla de promedios
                const [avgRows] = await connection.query('SELECT COUNT(*) as count FROM promedios_diarios');
                console.log(`   📈 Promedios: ${avgRows[0].count} registros`);
                
                connection.release();
            } catch (error) {
                console.log(`   ❌ Error conectando a la base de datos: ${error.message}`);
            }
        }

        // 4. Probar inicialización del AverageService
        console.log('\n4️⃣ Probando inicialización del AverageService...');
        try {
            const AverageService = require('../services/averageService');
            const averageService = new AverageService();
            console.log('   ✅ AverageService inicializado correctamente');
            
            // Probar obtención de configuración
            const configuracion = await averageService.obtenerConfiguracionVariables();
            console.log(`   📋 Configuración obtenida: ${configuracion.length} variables`);
            
        } catch (error) {
            console.log(`   ❌ Error inicializando AverageService: ${error.message}`);
        }

        // 5. Probar inicialización del AverageController
        console.log('\n5️⃣ Probando inicialización del AverageController...');
        try {
            const AverageController = require('../controllers/averageController');
            const averageController = new AverageController();
            
            if (averageController.averageService) {
                console.log('   ✅ AverageController inicializado correctamente');
            } else {
                console.log('   ❌ AverageController NO tiene averageService');
            }
            
        } catch (error) {
            console.log(`   ❌ Error inicializando AverageController: ${error.message}`);
        }

        console.log('\n' + '=' .repeat(60));
        console.log('🎯 DIAGNÓSTICO COMPLETADO');

    } catch (error) {
        console.error('❌ Error en diagnóstico:', error);
    }
}

// Ejecutar diagnóstico
diagnoseAverages()
    .then(() => {
        console.log('\n✅ Diagnóstico completado');
        process.exit(0);
    })
    .catch((error) => {
        console.error('\n❌ Error en diagnóstico:', error);
        process.exit(1);
    }); 