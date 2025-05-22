import React from 'react';
import ChartWrapper from './ChartWrapper';

const defaultZones = [
  { value: 130, color: '#15b01a' },
  { value: 180, color: '#fbfb00' },
  { value: 230, color: '#ffa400' },
  { value: 330, color: '#ff0000' },
  { value: 10000, color: '#8a3d92' },
];

const AreaChart = ({ data, title, yAxisTitle = 'PM10 (µg/m³)', zones = defaultZones, width, height }) => {
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
      shared: true, // Muestra un tooltip compartido para todas las series
      xDateFormat: '%Y-%m-%d %H:%M:%S', // Formato para el timestamp
      pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: <b>{point.y}</b><br/>',
    },
    series: [
      {
        type: 'area',
        name: yAxisTitle,
        data,
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