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
  
  console.log('ForecastChart recibió props:', { title, forecastData: forecastData?.length, realData: realData?.length, rangeData: rangeData?.length });

  useEffect(() => {
    if (!chartRef.current) {
      console.log('ForecastChart: chartRef.current es null');
      return;
    }
    
    console.log('Creando chart de Highcharts con datos:', { 
      forecastData: forecastData?.length, 
      realData: realData?.length, 
      rangeData: rangeData?.length 
    });

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
            text: 'SO2 (ug/m3)'
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
            data: realData || [],
            type: 'line',
            turboThreshold: 0,
            zIndex: 1,
            marker: {
              fillColor: 'black',
              lineWidth: 2
            },
            lineColor: '#000000'
          }
        ]
      });

      console.log('Chart creado exitosamente:', chart);

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