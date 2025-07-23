import React, { useMemo } from 'react';
import Highcharts from 'highcharts';
import HighchartsReact from 'highcharts-react-official';

const WindRosePolarChart = ({ data, title, width, height, backgroundColor }) => {
  // Procesar los datos para crear rangos de velocidad
  const processedData = useMemo(() => {
    console.log('🌹 Procesando datos para rosa de vientos:', data.length, 'registros');
    
    if (!data || data.length === 0) {
      console.log('🌹 No hay datos para procesar');
      return [];
    }

    // Mostrar algunos ejemplos de datos
    if (data.length > 0) {
      console.log('🌹 Ejemplos de datos:', data.slice(0, 3));
    }

    // Definir rangos de velocidad
    const speedRanges = [
      { name: '< 0.5 m/s', min: 0, max: 0.5, color: '#2A3Bf7' },// azul
      { name: '0.5 - 3 m/s', min: 0.5, max: 3, color: '#0Ef947' },//verde
      { name: '3 - 6 m/s', min: 3, max: 6, color: '#FAF514' },//Amarillo
      { name: '6 - 8 m/s', min: 6, max: 8, color: '#FA7216' },//Naranja
      { name: '> 8 m/s', min: 8, max: Infinity, color: '#FA0E05' },//Rojo
    ];

    // Definir direcciones cardinales
    const directions = [
      'N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE',
      'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'
    ];

    // Función para convertir grados a dirección cardinal
    const degreesToDirection = (degrees) => {
      const normalized = (degrees + 360) % 360;
      const index = Math.round(normalized / 22.5) % 16;
      return directions[index];
    };

    // Agrupar datos por dirección y rango de velocidad
    const groupedData = {};
    
    // Inicializar estructura
    directions.forEach(dir => {
      groupedData[dir] = {};
      speedRanges.forEach(range => {
        groupedData[dir][range.name] = 0;
      });
    });

    // Procesar datos
    let processedCount = 0;
    let totalValidRecords = 0;
    
    data.forEach(({ velocidad, direccion }) => {
      if (velocidad !== null && direccion !== null && velocidad >= 0) {
        const direction = degreesToDirection(direccion);
        const range = speedRanges.find(r => velocidad >= r.min && velocidad < r.max);
        
        if (range && groupedData[direction]) {
          groupedData[direction][range.name]++;
          processedCount++;
        }
        totalValidRecords++;
      }
    });

    console.log('🌹 Datos procesados:', processedCount, 'de', data.length, 'registros');

    // Calcular porcentajes en lugar de conteos absolutos
    const totalRecords = Math.max(totalValidRecords, 1); // Evitar división por cero
    
    // Convertir a formato de series para Highcharts con porcentajes
    const series = speedRanges.map(range => ({
      name: range.name,
      color: range.color,
      data: directions.map(dir => {
        const count = groupedData[dir][range.name] || 0;
        const percentage = (count / totalRecords) * 100;
        return [dir, parseFloat(percentage.toFixed(2))];
      })
    }));

    console.log('🌹 Series generadas:', series.length, 'rangos de velocidad');
    
    // Mostrar estadísticas de cada serie
    series.forEach(serie => {
      const total = serie.data.reduce((sum, [dir, value]) => sum + value, 0);
      console.log(`🌹 ${serie.name}: ${total.toFixed(2)}%`);
    });

    return series;
  }, [data]);

  const options = {
    chart: {
      polar: true,
      type: 'column',
      width: width,
      height: height,
      backgroundColor: backgroundColor || 'transparent',
      style: {
        fontFamily: 'Arial, sans-serif'
      }
    },
    title: {
      text: title || 'Rosa de los Vientos',
      align: 'left',
      style: {
        fontSize: '16px',
        fontWeight: 'bold',
        color: '#333'
      }
    },
    subtitle: {
      text: 'Distribución de velocidad del viento por dirección',
      align: 'left',
      style: {
        fontSize: '12px',
        color: '#666'
      }
    },
    pane: {
      size: '85%',
      backgroundColor: backgroundColor || 'transparent',
      borderWidth: 0,
      background: {
        backgroundColor: backgroundColor || 'transparent'
      }
    },
    xAxis: {
      tickmarkPlacement: 'on',
      lineWidth: 0,
      categories: ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE',
                   'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'],
      labels: {
        style: {
          fontSize: '10px',
          color: '#333'
        }
      }
    },
    yAxis: {
      gridLineInterpolation: 'polygon',
      lineWidth: 0,
      min: 0,
      endOnTick: false,
      showLastLabel: true,
      title: {
        text: 'Frecuencia (%)',
        style: {
          fontSize: '12px',
          color: '#333'
        }
      },
      labels: {
        formatter: function() {
          return this.value + '%';
        },
        style: {
          fontSize: '10px',
          color: '#666'
        }
      },
      reversedStacks: false,
      gridLineColor: '#E6E6E6',
      gridLineWidth: 1
    },
    tooltip: {
      shared: true,
      valueSuffix: '%',
      pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y}%</b></span><br/>',
      backgroundColor: 'rgba(255, 255, 255, 0.95)',
      borderColor: '#CCC',
      borderRadius: 5,
      shadow: true,
      style: {
        fontSize: '11px'
      }
    },
    plotOptions: {
      series: {
        stacking: 'normal',
        shadow: {
          opacity: 0
        },
        groupPadding: 0,
        pointPlacement: 'on',
        borderWidth: 0,
        dataLabels: {
          enabled: false
        }
      },
      column: {
        pointPadding: 0,
        groupPadding: 0,
        borderWidth: 0
      }
    },
    legend: {
      align: 'right',
      verticalAlign: 'top',
      y: 100,
      layout: 'vertical',
      backgroundColor: 'rgba(255, 255, 255, 0.8)',
      borderColor: '#CCC',
      borderWidth: 1,
      borderRadius: 5,
      shadow: true,
      itemStyle: {
        fontSize: '11px',
        color: '#333'
      },
      itemHoverStyle: {
        color: '#000'
      }
    },
    credits: {
      enabled: false
    },
    series: processedData
  };

  if (!data || data.length === 0) {
    return (
      <div style={{ 
        height: height || 300, 
        display: 'flex', 
        alignItems: 'center', 
        justifyContent: 'center', 
        border: '1px solid #ccc',
        backgroundColor: backgroundColor || 'transparent',
        borderRadius: '8px',
        color: '#666',
        fontSize: '14px'
      }}>
        No hay datos de viento disponibles
      </div>
    );
  }

  return (
    <div style={{ 
      backgroundColor: backgroundColor || 'transparent',
      borderRadius: '8px',
      padding: '10px'
    }}>
      <HighchartsReact highcharts={Highcharts} options={options} />
    </div>
  );
};

export default WindRosePolarChart; 