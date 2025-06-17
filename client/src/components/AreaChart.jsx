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
  // Ajusta este valor según la frecuencia real de tus datos (ejemplo: 1 min = 60000 ms)
  const INTERVAL_MS = 60 * 1000;

  // Solo toma puntos válidos y con timestamp numérico
  const cleanData = data
    .filter(point => point && point.length === 2 && !isNaN(point[0]))
    .map(([timestamp, value]) => [Number(timestamp), value === 0 ? null : value]);

  // Rellenar los huecos
  const filledData = fillMissingTimestamps(cleanData, INTERVAL_MS);

  const options = {
    chart: { type: 'area', zoomType: 'x', animation: false, width: width, height: height },
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
        fillOpacity: 1,
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

  return <ChartWrapper options={options} />;
};

export default AreaChart;