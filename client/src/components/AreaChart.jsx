import React from 'react';
import ChartWrapper from './ChartWrapper';

const defaultZones = [
  { value: 130, color: '#15b01a' },
  { value: 180, color: '#fbfb00' },
  { value: 230, color: '#ffa400' },
  { value: 330, color: '#ff0000' },
  { value: 10000, color: '#8a3d92' },
];

// Rellena los huecos de tiempo con nulls para que Highcharts muestre huecos
function fillMissingTimestamps(data, intervalMs) {
  if (!data.length) return [];
  // Ordenar por timestamp
  const sorted = [...data].sort((a, b) => a[0] - b[0]);
  const filled = [];
  let [current, end] = [sorted[0][0], sorted[sorted.length - 1][0]];
  let i = 0;
  while (current <= end) {
    if (i < sorted.length && sorted[i][0] === current) {
      filled.push([current, sorted[i][1]]);
      i++;
    } else {
      filled.push([current, null]);
    }
    current += intervalMs;
  }
  return filled;
}

const AreaChart = ({
  data,
  title,
  yAxisTitle = 'PM10 (µg/m³)',
  zones = defaultZones,
  width,
  height
}) => {
  console.log('AreaChart recibió props:', { data, title, yAxisTitle, width, height });
  
  // Verificar si hay datos
  if (!data || data.length === 0) {
    console.log('AreaChart: No hay datos para mostrar');
    return <div style={{ height: height || 300, display: 'flex', alignItems: 'center', justifyContent: 'center', border: '1px solid #ccc' }}>
      No hay datos disponibles
    </div>;
  }

  // Solo toma puntos válidos y con timestamp numérico
  const cleanData = data
    .filter(point => point && point.length === 2 && !isNaN(point[0]) && point[1] !== null && !isNaN(point[1]))
    .map(([timestamp, value]) => [Number(timestamp), Number(value)])
    .sort((a, b) => a[0] - b[0]); // Ordenar por timestamp

  console.log('Datos limpios después del filtro:', cleanData.length, 'puntos');
  console.log('Primeros 5 puntos:', cleanData.slice(0, 5));
  console.log('Últimos 5 puntos:', cleanData.slice(-5));

  // Simplificar: no rellenar huecos por ahora
  const filledData = cleanData;
  
  console.log('Datos finales para Highcharts:', filledData.length, 'puntos');

  const options = {
    chart: { 
      type: 'area', 
      zoomType: 'x', 
      animation: false, 
      width: width, 
      height: height || 300
    },
    title: { text: title },
    xAxis: { type: 'datetime', ordinal: false },
    yAxis: {
      title: { text: yAxisTitle },
      min: 0,
    },
    legend: { enabled: false },
    plotOptions: {
      area: {
        connectNulls: false,
        marker: { radius: 2, enabled: false },
        lineWidth: 1,
        fillOpacity: 0.6,
        states: { hover: { lineWidth: 1 } },
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
        turboThreshold: 0,
        connectNulls: false,
        tooltip: { valueSuffix: ' µg/m³' },
        zones,
      },
    ],
  };

  console.log('Opciones de Highcharts:', options);

  return <ChartWrapper options={options} />;
};

export default AreaChart;