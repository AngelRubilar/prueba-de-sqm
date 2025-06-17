// src/components/StationMultiChart.js
import React from 'react';
import Highcharts from 'highcharts';
import HighchartsReact from 'highcharts-react-official';

const StationMultiChart = ({ title, data }) => {
  // Procesar los datos para el gráfico
  const processChartData = () => {
    const chartData = {
      HR: [],
      PM10: [],
      VV: [],
      Temperatura: [],
      SO2: []
    };

    data.forEach(item => {
      const timestamp = new Date(item.timestamp).getTime();
      const value = parseFloat(item.valor);
      
      if (!isNaN(value)) {
        chartData[item.variable_name].push([timestamp, value]);
      }
    });

    // Ordenar cada serie por timestamp
    Object.keys(chartData).forEach(key => {
      chartData[key].sort((a, b) => a[0] - b[0]);
    });

    return chartData;
  };

  const chartData = processChartData();

  const options = {
    chart: {
      zoomType: 'xy',
      height: 400,
      spacing: [10, 10, 10, 10], // Ajusta el espaciado interno
      margin: [80, 50, 50, 50]   // Ajusta los márgenes [top, right, bottom, left]
    },
    title: {
      text: title
    },
    subtitle: {
      text: `Material Particulado Grueso: ${chartData.PM10[chartData.PM10.length - 1]?.[1]?.toFixed(2) || 'N/A'} ug/m3`
    },
    xAxis: {
      type: 'datetime',
      ordinal: false
    },
    yAxis: [{
      labels: {
        format: '{value} °C',
        style: {
          color: Highcharts.getOptions().colors[2]
        }
      },
      title: {
        text: 'Temperatura',
        style: {
          color: Highcharts.getOptions().colors[2]
        }
      },
      opposite: true
    }, {
      gridLineWidth: 0,
      title: {
        text: 'Velocidad del Viento',
        style: {
          color: Highcharts.getOptions().colors[0]
        }
      },
      labels: {
        format: '{value} m/s',
        style: {
          color: Highcharts.getOptions().colors[0]
        }
      }
    }, {
      gridLineWidth: 0,
      title: {
        text: 'Humedad Relativa',
        style: {
          color: Highcharts.getOptions().colors[1]
        }
      },
      labels: {
        format: '{value} %',
        style: {
          color: Highcharts.getOptions().colors[1]
        }
      },
      opposite: true
    }, {
      gridLineWidth: 0,
      title: {
        text: 'PM10',
        style: {
          color: Highcharts.getOptions().colors[3]
        }
      },
      labels: {
        format: '{value} ug/m3',
        style: {
          color: Highcharts.getOptions().colors[3]
        }
      }
    }, {
      gridLineWidth: 0,
      title: {
        text: 'SO2',
        style: {
          color: Highcharts.getOptions().colors[4]
        }
      },
      labels: {
        format: '{value} ppbv',
        style: {
          color: Highcharts.getOptions().colors[4]
        }
      },
      opposite: true
    }],
    tooltip: {
      shared: true
    },
    legend: {
      layout: 'horizontal',
      align: 'center',
      x: 0,
      verticalAlign: 'top',
      y: 50,
      floating: true,
      backgroundColor: 'rgba(255,255,255,0.25)',
      itemStyle: {
        fontSize: '11px'     // Tamaño de fuente más pequeño para mejor ajuste
      },
      itemDistance: 20,      // Espacio entre elementos de la leyenda
      symbolWidth: 10,       // Ancho del símbolo de la leyenda
      symbolHeight: 10       // Alto del símbolo de la leyenda
    },
    series: [{
      name: 'Velocidad del Viento',
      type: 'column',
      yAxis: 1,
      data: chartData.VV,
      tooltip: {
        valueSuffix: ' m/s'
      }
    }, {
      name: 'Humedad Relativa',
      type: 'spline',
      yAxis: 2,
      data: chartData.HR,
      marker: {
        enabled: false
      },
      dashStyle: 'shortdot',
      tooltip: {
        valueSuffix: ' %'
      }
    }, {
      name: 'Temperatura',
      type: 'spline',
      data: chartData.Temperatura,
      tooltip: {
        valueSuffix: ' °C'
      }
    }, {
      name: 'PM10',
      type: 'spline',
      yAxis: 3,
      data: chartData.PM10,
      tooltip: {
        valueSuffix: ' ug/m3'
      }
    }, {
      name: 'SO2',
      type: 'spline',
      yAxis: 4,
      data: chartData.SO2,
      tooltip: {
        valueSuffix: ' ppbv'
      }
    }]
  };

  return (
    <div className="station-chart">
      <HighchartsReact
        highcharts={Highcharts}
        options={options}
      />
    </div>
  );
};

export default StationMultiChart;