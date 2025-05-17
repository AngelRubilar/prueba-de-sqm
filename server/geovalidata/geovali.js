const axios = require('axios');
const fs = require('fs'); // Importar el módulo fs
const path = require('path');
const { writerPool } = require('../db'); // Importar writerPool desde db.js

const config = {
  method: 'post',
  maxBodyLength: Infinity,
  url: 'https://sbem-sb-prd.servicebus.windows.net/sbem-sbt-prd/subscriptions/particulas/messages/head',
  headers: { 
    'Authorization': 'SharedAccessSignature sr=https%3A%2F%2Fsbem-sb-prd.servicebus.windows.net%2Fsbem-sbt-prd&sig=i%2BuNvoaDY%2B/c%2B0gNJtKFvKo%2BMesHaR5w7xW78BvnqVQ%3D&se=1744560198&skn=sbem_ap',
    'Cache-Control': 'no-cache',
    'User-Agent': 'PostmanRuntime/7.39.1',
    'Accept': '*/*',
    'Accept-Encoding': 'gzip, deflate, br',
    'Connection': 'keep-alive',
    'Content-Type': 'application/json'
  }
};

const nombreEstaciones = {
  "eui-24e124454c142255": "E11", // proveedor geovalidata estacion Muelle 1
  "eui-24e124454c142648": "E12", // proveedor geovalidata estacion Nueva Victoria
  "eui-24e124454c244455": "E13", // proveedor geovalidata estacion Sur Viejo
  "eui-24e124454c248348": "E14", // proveedor geovalidata estacion Coya Sur
  "eui-24e124454c142724": "E15", // proveedor geovalidata estacion Covadonga
  "eui-24e124454c144364": "Desconocida", // proveedor geovalidata estacion desconocida
};

function fetchDataAndSave() {
  //para revisar la solicitud
  console.log('Configuración de la solicitud:', JSON.stringify(config, null, 2));
  axios.request(config)
    .then(async (response) => {
      const responseData = response.data;
      console.log(response.status);
      console.log('Respuesta completa:', JSON.stringify(responseData, null, 2));
      //console.log('Tamaño de la respuesta:', JSON.stringify(responseData).length);

      // Guardar la respuesta en un archivo JSON
      const filePath = path.join(__dirname, 'geovali.json');
      console.log(`Guardando datos en: ${filePath}`);

      fs.writeFile(filePath, JSON.stringify(responseData, null, 2), (err) => {
        if (err) {
          console.error('Error al guardar el archivo JSON:', err);
        } else {
          console.log('Datos guardados en archivo JSON');
        }
      });

      // Transformar los datos recibidos al formato requerido
      const transformedData = transformData(responseData);
      console.log('Datos transformados:', JSON.stringify(transformedData, null, 2));

      // Guardar los datos transformados en MySQL
      await guardarDatosEnMySQL(transformedData);
    })
    .catch((error) => {
      if (error.response) {
        // El servidor respondió con un código de estado fuera del rango 2xx
        console.log(' geovalidata Error en la solicitud:', error.response.status);
        console.log(' geovalidata Respuesta del servidor:', error.response.data);
      } else if (error.request) {
        // La solicitud fue hecha pero no se recibió respuesta
        console.log(' geovalidata No se recibió respuesta del servidor:', error.request);
      } else {
        // Algo sucedió al configurar la solicitud que desencadenó un error
        console.log(' geovalidata Error en la configuración de la solicitud:', error.message);
      }
    });
}

function transformData(responseData) {
  const stationMappings = {
    "eui-24e124454c142255": {
      station: "E11",
      channels: {
        "chn1": "Temp",
        "chn2": "HR",
        "chn3": "VV",
        "chn4": "DV",
        "chn5": "Batt"
      }
    },
    "eui-24e124454c142648": { 
      station: "E12",
      channels: {
        "chn1": "Temp",
        "chn2": "HR",
        "chn3": "VV",
        "chn4": "DV",
        "chn5": "PS",
        "chn6": "Batt",
        "battery": "Estado de la batería"
      }
    },
    "eui-24e124454c244455": {
      station: "E13",
      channels: {
        "chn1": "Temp",
        "chn2": "HR",
        "chn3": "VV",
        "chn4": "DV",
        "chn5": "pluviometro",
        "chn6": "RS",
        "chn7": "evaporacion",
        "chn8": "PS",
        "chn9": "Batt"
      }
    },
    "eui-24e124454c248348": {
      station: "E14",
      channels: {
        "chn1": "Temp",
        "chn2": "HR",
        "chn3": "VV",
        "chn4": "DV",
        "chn5": "pluviometro",
        "chn6": "RS",
        "chn7": "evaporacion",
        "chn8": "Batt"
      }
    },
    "eui-24e124454c142724": {	
      station: "E15",
      channels: {
        "chn1": "VV",
        "chn2": "DR",
        "chn3": "Batt"
      }
    },
  };

  const deviceId = responseData.device_id;
  console.log('Device ID:', deviceId);
  const stationName = nombreEstaciones[deviceId];
  if (!stationName) {
    console.warn(`El deviceId ${deviceId} no se encuentra en nombreEstaciones`);
  }
  console.log('Station Name:', stationName);
  const stationInfo = stationMappings[deviceId];
  console.log(`Device ID: ${deviceId}, Station Name: ${stationName}, Station Info: ${JSON.stringify(stationInfo)}`);
  
  if (!stationInfo) return [];

  const timestamp = responseData.received_at.replace('T', ' ').split('.')[0];
  const results = [];

  for (const chnKey in responseData.channels) {
    const valor = responseData.channels[chnKey];
    const variableName = stationInfo.channels[chnKey];
    console.log(`Procesando canal ${chnKey}: variableName=${variableName}, valor=${valor}`);
    results.push({
      timestamp,
      "station_name": stationName,
      "variable_name": variableName,
      valor
    });
  }
  console.log('Resultados transformados:', results);
  return results;
}

// Función para guardar los datos en MySQL
async function guardarDatosEnMySQL(datosArray) {
  const connection = await writerPool.getConnection();
  const queryInsert = 'INSERT INTO datos (timestamp, station_name, variable_name, valor) VALUES ?';
  const queryCheck = 'SELECT timestamp, station_name, variable_name FROM datos WHERE (timestamp, station_name, variable_name) IN (?)';

  const datosParaInsertar = [];

  // Crear un conjunto de claves únicas para verificar la existencia
  const claves = datosArray.map(({ timestamp, station_name, variable_name }) => [timestamp, station_name, variable_name]);

  // Verificar la existencia de los registros en una sola consulta
  const [rows] = await connection.query(queryCheck, [claves]);

  // Crear un conjunto de claves existentes para una búsqueda rápida
  const clavesExistentes = new Set(rows.map(row => `${row.timestamp}-${row.station_name}-${row.variable_name}`));

  for (const datos of datosArray) {
    const { timestamp, station_name, variable_name, valor } = datos;
    const clave = `${timestamp}-${station_name}-${variable_name}`;
    if (!clavesExistentes.has(clave)) {
      datosParaInsertar.push([timestamp, station_name, variable_name, valor]);
    } else {
      console.log(`El registro ya existe para ${timestamp}, ${station_name}, ${variable_name}`);
    }
  }

  if (datosParaInsertar.length > 0) {
    await connection.query(queryInsert, [datosParaInsertar]);
    console.log('Datos guardados correctamente en la base de datos');
  } else {
    console.log('No hay datos nuevos para insertar en la base de datos.');
  }

  connection.release();
}

module.exports = fetchDataAndSave;