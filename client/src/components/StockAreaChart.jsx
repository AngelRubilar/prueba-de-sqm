import React, { useEffect, useRef } from 'react';
import Highcharts from 'highcharts/highstock';
import HighchartsReact from 'highcharts-react-official';

// Establecer idioma global para el selector de rango
Highcharts.setOptions({
  lang: {
    rangeSelectorZoom: 'Rango'
  }
});

const defaultZones = [
  { value: 130, color: '#15b01a' },
  { value: 180, color: '#fbfb00' },
  { value: 230, color: '#ffa400' },
  { value: 330, color: '#ff0000' },
  { value: 10000, color: '#8a3d92' },
];

function fillMissingTimestamps(data, expectedIntervalMs = 5 * 60 * 1000) {
  if (!data.length) return [];
  const sorted = [...data].sort((a, b) => a[0] - b[0]);
  const filled = [];
  for (let i = 0; i < sorted.length; i++) {
    filled.push(sorted[i]);
    if (i < sorted.length - 1) {
      const currentTime = sorted[i][0];
      const nextTime = sorted[i + 1][0];
      const timeDiff = nextTime - currentTime;
      if (timeDiff > expectedIntervalMs * 1.5) {
        filled.push([currentTime + expectedIntervalMs, null]);
        if (timeDiff > expectedIntervalMs * 2.5) {
          filled.push([nextTime - expectedIntervalMs, null]);
        }
      }
    }
  }
  return filled;
}

const StockAreaChart = ({
  data,
  title = '',
  yAxisTitle = '',
  zones = defaultZones,
  width,
  height = 350,
  expectedInterval = 5 * 60 * 1000,
  showNormaAmbiental = false,
  normaAmbientalValue = 130
}) => {
  const chartComponentRef = useRef(null);

  if (!data || data.length === 0) {
    return (
      <div style={{ height: height || 300, display: 'flex', alignItems: 'center', justifyContent: 'center', border: '1px solid #ccc' }}>
        No hay datos disponibles
      </div>
    );
  }

  const cleanData = data
    .filter(point => point && point.length === 2 && !isNaN(point[0]) && point[1] !== null && !isNaN(point[1]))
    .map(([timestamp, value]) => [Number(timestamp), Number(value)])
    .sort((a, b) => a[0] - b[0]);

  const filledData = fillMissingTimestamps(cleanData, expectedInterval);

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
      width: width,
      height: height,
      zoomType: 'x',
      animation: false
    },
    title: { text: title },
    xAxis: {
      type: 'datetime',
      ordinal: false,
      gapGridLineWidth: 0
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
        connectNulls: false,
        gapSize: 2,
        gapUnit: 'relative',
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
        zones
      },
    },
    tooltip: {
      shared: true,
      xDateFormat: '%Y-%m-%d %H:%M:%S',
      pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: <b>{point.y}</b><br/>'
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
        fillColor: {
          linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
          stops: [
            [0, Highcharts.getOptions().colors[0]],
            [1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0.25).get()]
          ]
        },
        threshold: null
      }
    ],
    rangeSelector: {
      buttons: [
        { type: 'hour', count: 1, text: '1h' },
        { type: 'day', count: 1, text: '1D' },
        { type: 'all', count: 1, text: 'Todo' }
      ],
      selected: 1,
      inputEnabled: false
    },
    navigator: {
      enabled: true,
      adaptToUpdatedData: true,
      series: {
        type: 'area',
        color: '#7cb5ec',
        fillOpacity: 0.2
      }
    },
    credits: { enabled: false },
    lang: {
      rangeSelectorZoom: 'Rango'
    }
  };

  return <HighchartsReact highcharts={Highcharts} constructorType="stockChart" options={options} ref={chartComponentRef} />;
};

export default StockAreaChart; 