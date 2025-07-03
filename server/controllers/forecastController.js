// server/controllers/forecastController.js
const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs').promises;

// Función alternativa para leer el archivo parquet usando Python
async function readParquetFileWithPython(filePath) {
  return new Promise((resolve, reject) => {
    console.log('Leyendo archivo parquet con Python...');
    
    const pythonScript = `
import pandas as pd
import json
import sys

try:
    # Leer el archivo parquet
    df = pd.read_parquet('${filePath}')
    
    # Convertir a JSON
    result = df.to_json(orient='records', date_format='iso')
    print(result)
    
except Exception as e:
    print(f"ERROR: {str(e)}", file=sys.stderr)
    sys.exit(1)
`;

    const pythonProcess = spawn('python3', ['-c', pythonScript]);

    let pythonOutput = '';
    let pythonError = '';

    pythonProcess.stdout.on('data', (data) => {
      pythonOutput += data.toString();
    });

    pythonProcess.stderr.on('data', (data) => {
      pythonError += data.toString();
    });

    pythonProcess.on('close', (code) => {
      if (code === 0) {
        try {
          const data = JSON.parse(pythonOutput);
          console.log(`Archivo parquet leído exitosamente: ${data.length} registros`);
          resolve(data);
        } catch (error) {
          reject(new Error(`Error parsing JSON: ${error.message}`));
        }
      } else {
        reject(new Error(`Python process exited with code ${code}. Error: ${pythonError}`));
      }
    });

    pythonProcess.on('error', (error) => {
      reject(error);
    });
  });
}

// Función para ejecutar el script Python
async function runPythonScript() {
  return new Promise((resolve, reject) => {
    console.log('Iniciando script Python...');
    
    const pythonProcess = spawn('python3', ['getForecast.py'], {
      cwd: path.join(__dirname, '../python')
    });

    let pythonOutput = '';
    let pythonError = '';

    pythonProcess.stdout.on('data', (data) => {
      pythonOutput += data.toString();
      console.log('Python stdout:', data.toString());
    });

    pythonProcess.stderr.on('data', (data) => {
      pythonError += data.toString();
      console.error('Python stderr:', data.toString());
    });

    pythonProcess.on('close', (code) => {
      console.log(`Script Python terminado con código: ${code}`);
      if (code === 0) {
        resolve(pythonOutput);
      } else {
        reject(new Error(`Python process exited with code ${code}. Error: ${pythonError}`));
      }
    });

    pythonProcess.on('error', (error) => {
      console.error('Error ejecutando script Python:', error);
      reject(error);
    });
  });
}

// Función para verificar si el archivo parquet existe y es reciente
async function checkParquetFile(filePath) {
  try {
    const stats = await fs.stat(filePath);
    const fileAge = Date.now() - stats.mtime.getTime();
    const maxAge = 2 * 60 * 60 * 1000; // 2 horas en milisegundos

    if (fileAge > maxAge) {
      console.log('Archivo parquet es muy antiguo, necesitamos actualizarlo');
      return false;
    }

    return true;
  } catch (error) {
    console.error('Error verificando archivo parquet:', error);
    return false;
  }
}

const getSO2Forecast = async (req, res) => {
  try {
    //console.log('=== INICIANDO OBTENCIÓN DE PRONÓSTICO ===');
    
    const parquetPath = path.join(__dirname, '../python/databases/fcst_db.parquet');
    //console.log('Ruta del archivo parquet:', parquetPath);

    // Verificar si necesitamos actualizar el pronóstico
    const needsUpdate = !(await checkParquetFile(parquetPath));
    
    if (needsUpdate) {
      console.log('Ejecutando script Python para actualizar pronóstico...');
      await runPythonScript();
    } else {
      console.log('Usando archivo parquet existente...');
    }

    // Verificar si el archivo existe después de la actualización
    try {
      await fs.access(parquetPath);
    } catch (error) {
      console.error('Archivo parquet no encontrado después de la actualización:', error);
      return res.status(404).json({ 
        error: 'Archivo de pronóstico no encontrado',
        details: 'El script Python no generó el archivo de pronóstico'
      });
    }

    // Leer y procesar los datos del parquet usando Python
    console.log('Leyendo datos del archivo parquet...');
    const rawData = await readParquetFileWithPython(parquetPath);
    console.log(`Datos crudos leídos: ${rawData.length} registros`);
    
    // Filtrar datos de los últimos 2 días
    const twoDaysAgo = new Date(Date.now() - 2 * 24 * 60 * 60 * 1000);
    const filteredData = rawData.filter(d => {
      const dataDate = new Date(d.ds);
      return dataDate >= twoDaysAgo;
    });
    //console.log(`Datos filtrados (últimos 2 días): ${filteredData.length} registros`);

    // Procesar los datos para el frontend
    const processedData = filteredData.map(d => ({
      ds: d.ds,
      yhat: Number(d.yhat) || 0,
      y: Number(d.y) || 0,
      yhat_lower: Number(d.yhat_lower) || 0,
      yhat_upper: Number(d.yhat_upper) || 0,
      station: d.station
    }));

    //console.log(`Datos procesados: ${processedData.length} registros`);
    
    // Agrupar datos por estación
    const dataByStation = {
      'Huara': processedData.filter(d => d.station === 'Huara'),
      'Victoria': processedData.filter(d => d.station === 'Victoria'),
      'Colonia Pintados': processedData.filter(d => d.station === 'Colonia Pintados')
    };
    
    //console.log('=== DATOS AGRUPADOS POR ESTACIÓN ===');
  /*   Object.keys(dataByStation).forEach(station => {
      console.log(`${station}: ${dataByStation[station].length} registros`);
      if (dataByStation[station].length > 0) {
        console.log(`  Ejemplo de datos para ${station}:`, dataByStation[station][0]);
      }
    });
     */
    // Verificar que los datos no estén vacíos
    const totalRecords = Object.values(dataByStation).reduce((sum, stationData) => sum + stationData.length, 0);
    //console.log(`Total de registros a enviar: ${totalRecords}`);
    
    if (totalRecords === 0) {
      console.log('ADVERTENCIA: No hay datos para enviar al frontend');
    }
    
    // Enviar respuesta con datos agrupados por estación
    //console.log('Enviando respuesta al frontend...');
    res.json(dataByStation);

  } catch (error) {
    console.error('Error en getSO2Forecast:', error);
    res.status(500).json({ 
      error: 'Error al obtener datos del pronóstico',
      details: error.message
    });
  }
};
// Controlador para forzar la actualización del pronóstico
const forceForecastUpdate = async (req, res) => {
  try {
    //console.log('Forzando actualización del pronóstico...');
    await runPythonScript();
    
    res.json({ 
      message: 'Pronóstico actualizado exitosamente',
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    console.error('Error en forceForecastUpdate:', error);
    res.status(500).json({ 
      error: 'Error al actualizar el pronóstico',
      details: error.message
    });
  }
};

// Controlador para obtener el estado del pronóstico
const getForecastStatus = async (req, res) => {
  try {
    const parquetPath = path.join(__dirname, '../python/databases/fcst_db.parquet');
    
    try {
      const stats = await fs.stat(parquetPath);
      res.json({
        lastUpdate: stats.mtime,
        fileExists: true,
        fileSize: stats.size
      });
    } catch (error) {
      res.json({
        lastUpdate: null,
        fileExists: false,
        fileSize: 0
      });
    }
  } catch (error) {
    console.error('Error en getForecastStatus:', error);
    res.status(500).json({ 
      error: 'Error al obtener estado del pronóstico',
      details: error.message
    });
  }
};

// Controlador temporal para debug
const debugForecastData = async (req, res) => {
  try {
    //console.log('=== DEBUG FORECAST DATA ===');
    
    const parquetPath = path.join(__dirname, '../python/databases/fcst_db.parquet');
    //console.log('Ruta del archivo parquet:', parquetPath);
    
    // Verificar si el archivo existe
    try {
      const stats = await fs.stat(parquetPath);
      //console.log('Archivo existe, tamaño:', stats.size, 'bytes');
    } catch (error) {
      //console.log('Archivo NO existe:', error.message);
      return res.status(404).json({ error: 'Archivo no encontrado' });
    }
    
    // Intentar leer el archivo con Python
    //console.log('Intentando leer archivo parquet con Python...');
    const rawData = await readParquetFileWithPython(parquetPath);
    //console.log('Datos leídos:', rawData.length, 'registros');
    
    // Mostrar estructura de los primeros 3 registros
    if (rawData.length > 0) {
      //console.log('Estructura del primer registro:', Object.keys(rawData[0]));
      //console.log('Primeros 3 registros:', rawData.slice(0, 3));
    }
    
    // Filtrar datos de los últimos 2 días
    const twoDaysAgo = new Date(Date.now() - 2 * 24 * 60 * 60 * 1000);
    const filteredData = rawData.filter(d => {
      const dataDate = new Date(d.ds);
      return dataDate >= twoDaysAgo;
    });
    //console.log('Datos filtrados (últimos 2 días):', filteredData.length, 'registros');
    
    // Procesar datos
    const processedData = filteredData.map(d => ({
      ds: d.ds,
      yhat: Number(d.yhat) || 0,
      y: Number(d.y) || 0,
      yhat_lower: Number(d.yhat_lower) || 0,
      yhat_upper: Number(d.yhat_upper) || 0,
      station: d.station
    }));
    
    //console.log('Datos procesados:', processedData.length, 'registros');
    //console.log('=== FIN DEBUG ===');
    
    res.json({
      debug: true,
      rawDataCount: rawData.length,
      filteredDataCount: filteredData.length,
      processedDataCount: processedData.length,
      sampleData: processedData.slice(0, 5),
      filePath: parquetPath
    });
    
  } catch (error) {
    console.error('Error en debugForecastData:', error);
    res.status(500).json({ 
      error: 'Error en debug',
      details: error.message,
      stack: error.stack
    });
  }
};

module.exports = {
  getSO2Forecast,
  forceForecastUpdate,
  getForecastStatus,
  debugForecastData
};