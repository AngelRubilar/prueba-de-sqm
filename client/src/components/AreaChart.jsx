import React from 'react';
import ChartWrapper from './ChartWrapper';

const defaultZones = [
  { value: 130, color: '#15b01a' },
  { value: 180, color: '#fbfb00' },
  { value: 230, color: '#ffa400' },
  { value: 330, color: '#ff0000' },
  { value: 10000, color: '#8a3d92' },
];

// Función mejorada para detectar y llenar huecos temporales
function fillMissingTimestamps(data, expectedIntervalMs = 5 * 60 * 1000) { // 5 minutos por defecto
  if (!data.length) return [];
  
  // Ordenar por timestamp
  const sorted = [...data].sort((a, b) => a[0] - b[0]);
  const filled = [];
  
  for (let i = 0; i < sorted.length; i++) {
    filled.push(sorted[i]);
    
    // Si no es el último punto, verificar el gap al siguiente
    if (i < sorted.length - 1) {
      const currentTime = sorted[i][0];
      const nextTime = sorted[i + 1][0];
      const timeDiff = nextTime - currentTime;
      
      // Si el gap es mayor al intervalo esperado + tolerancia (50%)
      if (timeDiff > expectedIntervalMs * 1.5) {
        // Insertar un punto null después del punto actual
        filled.push([currentTime + expectedIntervalMs, null]);
        // Y otro antes del siguiente punto si es necesario
        if (timeDiff > expectedIntervalMs * 2.5) {
          filled.push([nextTime - expectedIntervalMs, null]);
        }
      }
    }
  }
  
  return filled;
}

const AreaChart = ({
  data,
  title,
  yAxisTitle = 'PM10 (µg/m³)',
  zones = defaultZones,
  width,
  height,
  expectedInterval = 5 * 60 * 1000, // 5 minutos en milisegundos
  showNormaAmbiental = false,
  normaAmbientalValue = 130
}) => {
  //console.log('AreaChart recibió props:', { data, title, yAxisTitle, width, height });
  
  // Verificar si hay datos
  if (!data || data.length === 0) {
    //console.log('AreaChart: No hay datos para mostrar');
    return (
      <div style={{ 
        height: height || 300, 
        display: 'flex', 
        alignItems: 'center', 
        justifyContent: 'center', 
        border: '1px solid #ccc' 
      }}>
        No hay datos disponibles
      </div>
    );
  }

  // Limpiar y procesar datos
  const cleanData = data
    .filter(point => point && point.length === 2 && !isNaN(point[0]) && point[1] !== null && !isNaN(point[1]))
    .map(([timestamp, value]) => [Number(timestamp), Number(value)])
    .sort((a, b) => a[0] - b[0]);

  //console.log('Datos limpios después del filtro:', cleanData.length, 'puntos');

  // Llenar huecos con valores null
  const filledData = fillMissingTimestamps(cleanData, expectedInterval);
  
  //console.log('Datos con huecos llenados:', filledData.length, 'puntos');

  // Determinar el texto de la norma ambiental según el tipo de contaminante
  const getNormaAmbientalText = () => {
    if (yAxisTitle.includes('SO₂')) {
      return 'Norma Ambiental: 350 μg/m³';
    } else if (yAxisTitle.includes('PM10')) {
      return 'Norma Ambiental: 130 μg/m³';
    }
    return `Norma Ambiental: ${normaAmbientalValue} μg/m³`;
  };

  const options = {
    chart: { 
      type: 'area', 
      zoomType: 'x', 
      animation: false, 
      width: width, 
      height: height || 300
    },
    title: { text: title },
    xAxis: { 
      type: 'datetime', 
      ordinal: false 
    },
    yAxis: {
      title: { text: yAxisTitle },
      min: 0,
      plotLines: showNormaAmbiental ? [
        {
          value: normaAmbientalValue,
          color: '#e74c3c',
          width: 2,
          dashStyle: 'dash',
          label: {
            text: getNormaAmbientalText(),
            style: {
              color: '#e74c3c',
              fontWeight: 'bold',
              fontSize: '11px'
            },
            rotation: 0,
            y: -5
          },
          zIndex: 5
        }
      ] : []
    },
    legend: { enabled: false },
    plotOptions: {
      area: {
        connectNulls: false, // CRÍTICO: No conectar valores null
        gapSize: 2, // Tamaño máximo del gap antes de romper la línea
        gapUnit: 'relative', // 'value' para milisegundos, 'relative' para puntos
        marker: { 
          radius: 2, 
          enabled: false,
          states: {
            hover: {
              enabled: true,
              radius: 4
            }
          }
        },
        lineWidth: 1,
        fillOpacity: 0.6,
        states: { 
          hover: { 
            lineWidth: 2 
          } 
        },
        threshold: null,
      },
    },
    tooltip: {
      shared: true,
      xDateFormat: '%Y-%m-%d %H:%M:%S',
      pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: <b>{point.y}</b><br/>',
    },
    series: [
      {
        type: 'area',
        name: yAxisTitle,
        data: filledData,
        turboThreshold: 0, // Permitir cualquier cantidad de puntos
        connectNulls: false, // Redundante pero explícito
        tooltip: { valueSuffix: ' µg/m³' },
        zones,
      },
    ],
  };

  //console.log('Opciones de Highcharts:', options);

  return <ChartWrapper options={options} />;
};

export default AreaChart;