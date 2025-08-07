require('dotenv').config();

console.log('🔍 Verificando variables de entorno...\n');

const requiredVars = [
    'DB_HOST',
    'DB_WRITER_USER',
    'DB_WRITER_PASSWORD',
    'DB_READER_USER',
    'DB_READER_PASSWORD',
    'DB_NAME'
];

console.log('1️⃣ Variables de entorno requeridas:');
requiredVars.forEach(varName => {
    const value = process.env[varName];
    if (value) {
        console.log(`   ✅ ${varName}: ${varName.includes('PASSWORD') ? '***' : value}`);
    } else {
        console.log(`   ❌ ${varName}: NO DEFINIDA`);
    }
});

console.log('\n2️⃣ Variables de entorno opcionales:');
const optionalVars = [
    'NODE_ENV',
    'PORT'
];

optionalVars.forEach(varName => {
    const value = process.env[varName];
    console.log(`   ${varName}: ${value || 'NO DEFINIDA'}`);
});

console.log('\n3️⃣ Verificando configuración de base de datos...');

const writerConfig = {
    host: process.env.DB_HOST || 'db',
    user: process.env.DB_WRITER_USER,
    password: process.env.DB_WRITER_PASSWORD,
    database: process.env.DB_NAME || 'datos_api'
};

const readerConfig = {
    host: process.env.DB_HOST || 'db',
    user: process.env.DB_READER_USER,
    password: process.env.DB_READER_PASSWORD,
    database: process.env.DB_NAME || 'datos_api'
};

console.log('   Writer config:', {
    host: writerConfig.host,
    user: writerConfig.user,
    database: writerConfig.database,
    password: writerConfig.password ? '***' : 'NO DEFINIDA'
});

console.log('   Reader config:', {
    host: readerConfig.host,
    user: readerConfig.user,
    database: readerConfig.database,
    password: readerConfig.password ? '***' : 'NO DEFINIDA'
});

// Verificar si todas las variables requeridas están definidas
const missingVars = requiredVars.filter(varName => !process.env[varName]);

if (missingVars.length > 0) {
    console.log('\n❌ Variables faltantes:', missingVars);
    console.log('   Esto puede causar problemas con la conexión a la base de datos');
} else {
    console.log('\n✅ Todas las variables requeridas están definidas');
} 