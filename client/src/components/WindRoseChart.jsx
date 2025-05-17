import React from 'react';
import Highcharts from 'highcharts';
import HighchartsReact from 'highcharts-react-official';
import HighchartsMore from 'highcharts/highcharts-more'; // Importa highcharts-more
import windbarb from 'highcharts/modules/windbarb'; // Importa windbarb
import exporting from 'highcharts/modules/exporting'; // Importa el módulo de exportación
import offlineExporting from 'highcharts/modules/offline-exporting'; // Importa el módulo de exportación offline

// Inicializa los módulos
HighchartsMore(Highcharts);
windbarb(Highcharts);
exporting(Highcharts);
offlineExporting(Highcharts); // Inicializa el módulo offline

const WindRoseChart = ({ data, title }) => {
  console.log('Datos recibidos por WindRoseChart:', data); // Verifica los datos recibidos

  // Ajusta los datos al formato esperado por Highcharts
  const processedData = data.map(({ velocidad, direccion }) => [parseFloat(velocidad) || 0, parseFloat(direccion) || 0]);

  console.log('Datos procesados para el gráfico:', processedData); // Verifica los datos procesados

  const options = {
    chart: {
      type: 'windbarb',
      height: 300,
    },
    title: {
      text: title,
    },
    xAxis: {
      type: 'datetime',
      title: {
        text: 'Tiempo',
      },
      labels: {
        format: '{value:%H:%M}', // Formato de tiempo (hora:minuto)
      },
    },
    yAxis: {
      title: {
        text: 'Velocidad del Viento (m/s)',
      },
      min: 0,
    },
    plotOptions: {
      series: {
        pointStart: Date.now(), // Define el inicio del eje X
        pointInterval: 36e5, // Intervalo de 1 hora (en milisegundos)
      },
    },
    exporting: {
      enabled: true, // Habilita el menú de exportación
      fallbackToExportServer: false, // Desactiva el uso del servidor externo
    },
    tooltip: {
      shared: true, // Muestra un tooltip compartido para todas las series
      xDateFormat: '%Y-%m-%d %H:%M', // Formato para el timestamp en el tooltip
      pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: <b>{point.y}</b> m/s<br/>',
    },
    series: [
      {
        type: 'windbarb',
        name: 'Dirección del Viento',
        data: processedData,
        color: Highcharts.getOptions().colors[1],
        tooltip: {
          valueSuffix: ' m/s',
        },
      },
      {
        type: 'area',
        keys: ['y'], // Solo se utiliza la velocidad (y) para esta serie
        data: processedData.map(([velocidad]) => [velocidad]), // Extrae solo la velocidad
        color: Highcharts.getOptions().colors[0],
        fillColor: {
          linearGradient: { x1: 0, x2: 0, y1: 0, y2: 1 },
          stops: [
            [0, Highcharts.getOptions().colors[0]],
            [
              1,
              Highcharts.color(Highcharts.getOptions().colors[0])
                .setOpacity(0.25)
                .get(),
            ],
          ],
        },
        name: 'Velocidad del Viento',
        tooltip: {
          valueSuffix: ' m/s',
        },
        states: {
          inactive: {
            opacity: 1,
          },
        },
      },
    ],
  };

  return <HighchartsReact highcharts={Highcharts} options={options} />;
};

export default WindRoseChart;