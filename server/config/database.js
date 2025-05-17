// ./server/config/database.js

// Asumiendo que usas mysql2, instala con: npm install mysql2
const mysql = require('mysql2/promise'); // Usando la versión con promesas
require('dotenv').config();
// Configuración para el usuario ESCRITOR (api_writer)
const writerConfig = {
  host: process.env.DB_HOST || 'db',
  user: process.env.DB_WRITER_USER,
  password: process.env.DB_WRITER_PASSWORD,
  database: process.env.DB_NAME || 'datos_api',
  waitForConnections: true,
  connectionLimit: 10, // Ajusta según tus necesidades
  queueLimit: 0
};

// Configuración para el usuario LECTOR (graphics_reader)
const readerConfig = {
  host: process.env.DB_HOST || 'db',
  user: process.env.DB_READER_USER,
  password: process.env.DB_READER_PASSWORD,
  database: process.env.DB_NAME || 'datos_api',
  waitForConnections: true,
  connectionLimit: 5, // Puede ser menor si solo es para lecturas de gráficos
  queueLimit: 0
};

// Crear pools de conexiones (recomendado para manejo eficiente de conexiones)
let writerPool;
let readerPool;

try {
  writerPool = mysql.createPool(writerConfig);
  console.log('Writer DB connection pool created successfully.');
} catch (error) {
  console.error('Failed to create writer DB connection pool:', error);
  // Podrías querer terminar la aplicación si la conexión principal falla
  // process.exit(1);
}

try {
  readerPool = mysql.createPool(readerConfig);
  console.log('Reader DB connection pool created successfully.');
} catch (error) {
  console.error('Failed to create reader DB connection pool:', error);
  // Para el lector, quizás solo logueas el error y continúas,
  // dependiendo de la criticidad de los gráficos.
}

// Exportar los pools para ser usados en otras partes de tu aplicación
module.exports = {
  writerPool,
  readerPool,
  // Si prefieres exportar una función para obtener una conexión de cada pool:
  // getWriterConnection: () => writerPool.getConnection(),
  // getReaderConnection: () => readerPool.getConnection(),
};