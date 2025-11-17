const fs = require('fs');
const path = require('path');
const moment = require('moment-timezone');

const STORE_PATH = path.resolve(__dirname, '../store.json');

function cargarTimestampSerpram() {
  try {
    const data = JSON.parse(fs.readFileSync(STORE_PATH, 'utf8'));
    return data.lastTimestampSerpram;
  } catch (e) {
    // Si el store no existe o está corrupto, pedimos los últimos 15 minutos
    return moment().tz('America/Santiago').subtract(15, 'minutes').toISOString();
  }
}

function guardarTimestampSerpram(nuevoTimestamp) {
  const data = { lastTimestampSerpram: nuevoTimestamp };
  fs.writeFileSync(STORE_PATH, JSON.stringify(data, null, 2));
}

module.exports = {
  cargarTimestampSerpram,
  guardarTimestampSerpram,
};
