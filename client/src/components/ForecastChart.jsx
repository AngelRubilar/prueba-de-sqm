import React, { useEffect, useRef } from 'react';
import Highcharts from 'highcharts';
import HighchartsMore from 'highcharts/highcharts-more';
import HighchartsExporting from 'highcharts/modules/exporting';
import HighchartsExportData from 'highcharts/modules/export-data';
import HighchartsAccessibility from 'highcharts/modules/accessibility';

// Inicializar módulos de Highcharts
HighchartsMore(Highcharts);
HighchartsExporting(Highcharts);
HighchartsExportData(Highcharts);
HighchartsAccessibility(Highcharts);

const ForecastChart = ({ title, forecastData, realData, rangeData }) => {
  const chartRef = useRef(null);

  // Obtener la hora actual
  const now = Date.now();

  
   
  // Filtrar realData hasta la hora actual y eliminar valores nulos
  const realDataFiltered = (realData || [])
    .filter(point => point[0] <= now && point[1] !== null && !isNaN(point[1]))
    .sort((a, b) => a[0] - b[0]);

  //console.log('realData filtrado:', realDataFiltered);

  useEffect(() => {
    if (!chartRef.current) return;

    try {
      const chart = Highcharts.chart(chartRef.current, {
        boost: {
          useGPUTranslations: true,
          usePreallocated: true
        },
        chart: {
          zooming: {
            type: 'x'
          },
          animation: false
        },
        title: {
          text: title
        },
        xAxis: {
          type: 'datetime',
          ordinal: false
        },
        yAxis: {
          title: {
            text: 'PM10 (ug/m3)'
          },
          min: 0
        },
        tooltip: {
          crosshairs: true,
          shared: true,
          valueSuffix: ' ug/m3',
          valueDecimals: 2
        },
        legend: {
          enabled: false
        },
        series: [
          {
            name: 'Pronóstico',
            data: forecastData || [],
            type: 'line',
            turboThreshold: 0,
            zIndex: 1,
            marker: {
              fillColor: 'white',
              lineWidth: 2
            },
            zones: [
              { value: 350, color: "#15b01a" },
              { value: 500, color: "#fbfb00" },
              { value: 650, color: "#ffa400" },
              { value: 950, color: "#ff0000" },
              { value: 10000, color: "#8a3d92" }
            ]
          },
          {
            name: 'Rango',
            data: rangeData || [],
            type: 'arearange',
            lineWidth: 0,
            linkedTo: ':previous',
            fillOpacity: 0.3,
            zIndex: 0,
            marker: {
              enabled: false
            },
            zones: [
              { value: 350, color: "#15b01a" },
              { value: 500, color: "#fbfb00" },
              { value: 650, color: "#ffa400" },
              { value: 950, color: "#ff0000" },
              { value: 10000, color: "#8a3d92" }
            ]
          },
          {
            name: 'Real',
            // Usar directamente los datos reales filtrados sin alineación forzada
            data: realDataFiltered,
            type: 'line',
            turboThreshold: 0,
            zIndex: 2, // Mayor zIndex para que esté por encima
            marker: {
              fillColor: 'black',
              lineWidth: 2,
              radius: 3
            },
            lineColor: '#000000',
            lineWidth: 3
          }
        ]
      });

      return () => {
        if (chart) {
          chart.destroy();
        }
      };
    } catch (error) {
      console.error('Error al crear el chart de Highcharts:', error);
    }
  }, [title, forecastData, realData, rangeData]);

  return (
    <div ref={chartRef} style={{ height: '400px' }} />
  );
};

export default ForecastChart;