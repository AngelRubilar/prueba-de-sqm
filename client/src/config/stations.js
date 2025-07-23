// Configuración específica para dashboard all estaciones
export const pm10Stations = [
  { title: 'Estación Victoria', station: 'E7' },
  { title: 'Estación Colonia Pintados', station: 'E8' },
  { title: 'Estación Victoria (sercoamb)', station: 'E10' },
  { title: 'Estación Huara',station: 'E6' },  
  { title: 'Estación Mejillones', station: 'E1' },
  { title: 'Estación Sierra Gorda', station: 'E2' },
  { title: 'Estación Tamentica', station: 'E9' },
  { title: 'Estacion Hospital Maria Elena PDA', station: 'E5' },  
];

export const so2Stations = [
  { title: 'Estación Mejillones', station: 'E1' },
  { title: 'Estación Sierra Gorda', station: 'E2' },
  { title: 'Estación Hospital Maria Elena',      station: 'E4' },
  { title: 'Estación Huara',         station: 'E6' },
  { title: 'Estación Victoria',      station: 'E7' },
  { title: 'Estación Colonia Pintados', station: 'E8' }
];

export const windStations = [
  { title: 'Estación Mejillones', station: 'E1' },
  { title: 'Estación Sierra Gorda', station: 'E2' },
  { title: 'Estacion Hospital Maria Elena PDA',      station: 'E5' },
  { title: 'Estación Huara',         station: 'E6' },
  { title: 'Estación Victoria',      station: 'E7' },
  { title: 'Estación Colonia Pintados', station: 'E8' },
  { title: 'Estación Tamentica', station: 'E9' },
  { title: 'Estación Victoria (sercoamb)', station: 'E10' }
];

// Configuración específica para sqm_grup1y
export const sqmGrup1yStations = [
  { title: 'Victoria', station: 'E7', showForecast: true },
  { title: 'Colonia Pintados', station: 'E8', showForecast: true },
  { title: 'Victoria (sercoamb)', station: 'E10', showForecast: false },
  { title: 'Huara', station: 'E6', showForecast: true },
  { title: 'Sur Viejo', station: 'E13', showForecast: false },
  { title: 'Tamentica (sercoamb)', station: 'E9', showForecast: false },
  { title: 'Nueva Victoria', station: 'E12', showForecast: false },
];

// Configuración específica para PM10 Grupo 1 con orden solicitado
export const pm10Grup1Stations = [
  { title: 'Estación Victoria', station: 'E7' },
  { title: 'Estación Colonia Pintados', station: 'E8' },
  { title: 'Estación Sur Viejo', station: 'E13' },
  { title: 'Estación Victoria (sercoamb)', station: 'E10' },
  { title: 'Estación Huara', station: 'E6' },
  { title: 'Estación Tamentica (sercoamb)', station: 'E9' },
  { title: 'Estación Nueva Victoria', station: 'E12' }
];

// Configuración específica para sqm_grup2 (ordenada)
export const sqmGrup2Stations = [
  { title: 'Hospital Maria Elena PDA', station: 'E5' },
  { title: 'Hospital Maria Elena', station: 'E4' },
  { title: 'Toco Norte', station: 'E16' },
  { title: 'Toco Sur', station: 'E17' },
  { title: 'Muelle 1 ', station: 'E11' },
  { title: 'Tocopilla', station: 'E18' },
  { title: 'Mejillones', station: 'E1' },
  { title: 'Sierra Gorda', station: 'E2' },
  { title: 'Coya Sur', station: 'E14' },
  { title: 'Covadonga', station: 'E15' },
];

// Mapeo de códigos de estación a nombres usados en forecastData
export const stationKeyMap = {
  E7: 'Victoria',
  E8: 'Colonia Pintados',
  E6: 'Huara'
};

// Configuración de variables disponibles por estación
// Basada en la información de disponibilidad de variables por estación
export const stationVariables = {
  'E1': { // Mejillones
    PM10: true,
    SO2: true,
    VV: true, // Velocidad del viento
    DV: true, // Dirección del viento
    HR: true, // Humedad relativa
    TEMP: true, // Temperatura
    PM25: true // PM2.5
  },
  'E2': { // Sierra Gorda
    PM10: true,
    SO2: true,
    VV: true,
    DV: true,
    HR: true,
    TEMP: true,
    PM25: true
  },
  'E4': { // Hospital Maria Elena
    PM10: false,
    SO2: true,
    VV: false,
    DV: false,
    HR: false,
    TEMP: true,
    PM25: true
  },
  'E5': { // Hospital Maria Elena PDA
    PM10: true,
    SO2: false,
    VV: true,
    DV: true,
    HR: true,
    TEMP: true,
    PM25: false
  },
  'E6': { // Huara
    PM10: true,
    SO2: true,
    VV: true,
    DV: true,
    HR: false,
    TEMP: true,
    PM25: false
  },
  'E7': { // Victoria
    PM10: true,
    SO2: true,
    VV: true,
    DV: true,
    HR: false,
    TEMP: true,
    PM25: true
  },
  'E8': { // Colonia Pintados
    PM10: true,
    SO2: true,
    VV: false,
    DV: true,
    HR: false,
    TEMP: true,
    PM25: true
  },
  'E9': { // Tamentica
    PM10: true,
    SO2: true,
    VV: true,
    DV: true,
    HR: true,
    TEMP: false,
    PM25: true
  },
  'E10': { // Victoria Sercoamb
    PM10: true,
    SO2: false,
    VV: true,
    DV: true,
    HR: false,
    TEMP: false,
    PM25: true
  },
  'E12': { // Nueva Victoria
    PM10: true,
    SO2: false,
    VV: true,
    DV: true,
    HR: false,
    TEMP: true,
    PM25: false
  },
  'E13': { // Sur Viejo
    PM10: true,
    SO2: false,
    VV: true,
    DV: true,
    HR: false,
    TEMP: true,
    PM25: false
  },
  'E11': { // Muelle 1
    PM10: true,
    SO2: false,
    VV: true,
    DV: true,
    HR: false,
    TEMP: true,
    PM25: false
  },
  'E14': { // Coya Sur
    PM10: true,
    SO2: false,
    VV: true,
    DV: true,
    HR: false,
    TEMP: true,
    PM25: false
  },
  'E15': { // Covadonga
    PM10: true,
    SO2: false,
    VV: true,
    DV: true,
    HR: false,
    TEMP: true,
    PM25: false
  },
  'E16': { // Toco Norte
    PM10: true,
    SO2: false,
    VV: true,
    DV: true,
    HR: false,
    TEMP: true,
    PM25: false
  },
  'E17': { // Toco Sur
    PM10: true,
    SO2: false,
    VV: true,
    DV: true,
    HR: false,
    TEMP: true,
    PM25: false
  },
  'E18': { // Tocopilla
    PM10: true,
    SO2: true,
    VV: true,
    DV: true,
    HR: true,
    TEMP: true,
    PM25: true
  }
};

// Función helper para verificar si una estación tiene una variable específica
export const hasVariable = (station, variable) => {
  return stationVariables[station]?.[variable] || false;
};