require('dotenv').config({ path: '../.env' });
const logAnalyzer = require('../utils/logAnalyzer');

// Ejecutar el reporte
async function runReport() {
    try {
        // Por defecto, analiza el último día
        await logAnalyzer.generateAndSendReport(1);
    } catch (error) {
        console.error('Error al ejecutar el reporte:', error);
    }
}

// Si se ejecuta directamente el script
if (require.main === module) {
    runReport();
}

module.exports = runReport; 