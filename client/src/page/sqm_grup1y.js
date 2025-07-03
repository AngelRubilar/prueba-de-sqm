import React, { useEffect, useState } from 'react';
import { fetchPM10Data, fetchSO2Data, fetchVientoData, fetchForecastData, fetchVariablesData } from '../services/api';
import AreaChart from '../components/AreaChart';
import ForecastChart from '../components/ForecastChart';
import SkeletonLoader from '../components/SkeletonLoader';
import estacionsqmImg from '../assets/estacionsqm.png';

// Mapeo de estaciones a nombres y tipos
const stations = [
  { code: 'E7', name: 'Victoria', showForecast: true },
  { code: 'E10', name: 'Victoria (sercoamb)', showForecast: false },
  { code: 'E8', name: 'Colonia Pintados', showForecast: true },
  { code: 'E12', name: 'Nueva Victoria', showForecast: false },
  { code: 'E13', name: 'Sur Viejo', showForecast: false },
  { code: 'E6', name: 'Huara', showForecast: true },
  { code: 'E9', name: 'Tamentica (sercoamb)', showForecast: false },
];

// Mapeo de códigos de estación a nombres usados en forecastData
const stationKeyMap = {
  E7: 'Victoria',
  E8: 'Colonia Pintados',
  E6: 'Huara'
};

function SqmGrup1y() {
  const [pm10Data, setPm10Data] = useState([]);
  const [so2Data, setSo2Data] = useState([]);
  const [windData, setWindData] = useState([]);
  const [variablesData, setVariablesData] = useState([]);
  const [forecastData, setForecastData] = useState({
    'Victoria': { forecast: [], real: [], range: [] },
    'Colonia Pintados': { forecast: [], real: [], range: [] },
    'Huara': { forecast: [], real: [], range: [] }
  });
  const [loading, setLoading] = useState(true);
  const [currentGroup, setCurrentGroup] = useState(0);

  // Función para cargar datos de PM10, SO2 y Viento (cada 5 minutos)
  const cargarDatosPrincipales = async () => {
    try {
      const [pm10, so2, viento, variables] = await Promise.all([
        fetchPM10Data(),
        fetchSO2Data(),
        fetchVientoData(),
        fetchVariablesData()
      ]);
      
      setPm10Data(pm10);
      setSo2Data(so2);
      setWindData(viento);
      setVariablesData(variables);
    } catch (error) {
      console.error('Error al cargar datos principales:', error);
    }
  };

  // Función para cargar datos de pronóstico (cada 1 hora)
  const cargarDatosPronostico = async () => {
    try {
      const pronostico = await fetchForecastData();
      setForecastData(pronostico);
    } catch (error) {
      console.error('Error al cargar datos de pronóstico:', error);
    }
  };

  // Carga inicial de todos los datos
  useEffect(() => {
    async function cargarDatosIniciales() {
      setLoading(true);
      try {
        await Promise.all([
          cargarDatosPrincipales(),
          cargarDatosPronostico()
        ]);
      } catch (error) {
        console.error('Error al cargar datos iniciales:', error);
      } finally {
        setLoading(false);
      }
    }
    cargarDatosIniciales();
  }, []);

  // Intervalo para datos principales (PM10, SO2, Viento) - cada 5 minutos
  useEffect(() => {
    const intervalPrincipales = setInterval(cargarDatosPrincipales, 300000); // 5 minutos
    return () => clearInterval(intervalPrincipales);
  }, []);

  // Intervalo para datos de pronóstico - cada 1 hora
  useEffect(() => {
    const intervalPronostico = setInterval(cargarDatosPronostico, 3600000); // 1 hora
    return () => clearInterval(intervalPronostico);
  }, []);

  // Carrusel automático cada 30 segundos
  useEffect(() => {
    const interval = setInterval(() => {
      setCurrentGroup((prev) => (prev + 1) % 2);
    }, 30000);
    return () => clearInterval(interval);
  }, []);

  // Dividir en grupos de 4 y 3 estaciones
  const groups = [
    stations.slice(0, 4),  // Primera vista: 4 estaciones
    stations.slice(4)      // Segunda vista: 3 estaciones
  ];

  const getUltimoSO2 = (station) => {
    const datos = so2Data.filter(d => d.station_name === station);
    if (!datos.length) return null;
    return datos[datos.length - 1].valor;
  };

  // Función para obtener el último valor de una variable específica
  const getUltimaVariable = (station, variable) => {
    const datos = variablesData.filter(d => d.station_name === station && d.variable_name === variable);
    if (!datos.length) return null;

    // Ordenar por timestamp y obtener el más reciente
    const datosOrdenados = datos.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
    return Number(datosOrdenados[0].valor);
  };

  // Funciones específicas para cada variable
  const getUltimaHR = (station) => getUltimaVariable(station, 'HR');
  const getUltimaTemperatura = (station) => getUltimaVariable(station, 'Temperatura');
  const getUltimaPM25 = (station) => getUltimaVariable(station, 'PM2_5');

  const getUltimoViento = (station) => {
    const datos = windData.filter(d => d.station_name === station);
    if (!datos.length) return { velocidad: 0, direccion: 0, timestamp: null };
    const ultimo = datos[datos.length - 1];
    return {
      velocidad: ultimo.velocidad,
      direccion: ultimo.direccion,
      timestamp: ultimo.timestamp
    };
  };

  const getSeriePM10 = (station) => {
    const data = pm10Data
      .filter(d => d.station_name === station)
      .map(d => [new Date(d.timestamp).getTime(), Number(d.valor) === 0 ? null : Number(d.valor)])
      .filter(point => {
        const [timestamp, value] = point;
        return !isNaN(timestamp) && !isNaN(value) && value !== null && value !== 0;
      });
    
    return data;
  };

  // Función para obtener datos de pronóstico por estación
  const getForecastDataForStation = (stationKey) => {
    const stationData = forecastData[stationKey];
  
    if (!stationData) {
      return {
        forecast: [],
        real: [],
        range: []
      };
    }
    const twoDaysAgo = Date.now() - 2 * 24 * 60 * 60 * 1000;
    const now = Date.now();
  
    // Filtrar, eliminar nulos y ordenar para forecast
    const forecastRaw = stationData.forecast
      .filter(d => d[0] >= twoDaysAgo && d[1] !== null && !isNaN(d[1]))
      .sort((a, b) => a[0] - b[0]);
    // Eliminar duplicados en forecast
    const uniqueForecast = [];
    const seenForecastTimestamps = new Set();
    for (const point of forecastRaw) {
      if (!seenForecastTimestamps.has(point[0])) {
        uniqueForecast.push(point);
        seenForecastTimestamps.add(point[0]);
      }
    }
  
    // Filtrar, eliminar nulos y ordenar para real (SOLO HASTA LA HORA ACTUAL)
    const realRaw = stationData.real
      .map(d => [d[0], (d[1] === 0 ? null : d[1])])
      .filter(d => d[0] >= twoDaysAgo && d[0] <= now && d[1] !== null && !isNaN(d[1]))
      .sort((a, b) => a[0] - b[0]);
    const uniqueReal = [];
    const seenRealTimestamps = new Set();
    for (const point of realRaw) {
      if (!seenRealTimestamps.has(point[0])) {
        uniqueReal.push(point);
        seenRealTimestamps.add(point[0]);
      }
    }
  
    // Filtrar, eliminar nulos y ordenar para range
    const rangeRaw = stationData.range
      .filter(d =>
        d[0] >= twoDaysAgo &&
        d[1] !== null && d[2] !== null &&
        !isNaN(d[1]) && !isNaN(d[2])
      )
      .sort((a, b) => a[0] - b[0]);
    // Eliminar duplicados en range
    const uniqueRange = [];
    const seenRangeTimestamps = new Set();
    for (const point of rangeRaw) {
      if (!seenRangeTimestamps.has(point[0])) {
        uniqueRange.push(point);
        seenRangeTimestamps.add(point[0]);
      }
    }
  
    return {
      forecast: uniqueForecast,
      real: uniqueReal,
      range: uniqueRange
    };
  };

  if (loading) {
    return (
      <div style={{
        minHeight: '100vh',
        background: 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
        padding: '20px 0'
      }}>
        <div style={{
          textAlign: 'center',
          marginBottom: 30,
          padding: '0 20px'
        }}>
          <h1 style={{
            fontSize: 28,
            fontWeight: 600,
            color: '#2c3e50',
            marginBottom: 10,
            fontFamily: 'Roboto, sans-serif'
          }}>
            Dashboard SQM - Superintendencia Operaciones Medio Ambiente Nueva Victoria
          </h1>
          <div style={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            gap: 10,
            color: '#7f8c8d',
            fontSize: 16
          }}>
            <div style={{
              width: 20,
              height: 20,
              border: '2px solid #3498db',
              borderTop: '2px solid transparent',
              borderRadius: '50%',
              animation: 'spin 1s linear infinite'
            }} />
            Cargando datos de las estaciones...
          </div>
        </div>
        <style>
          {`
            @keyframes spin {
              0% { transform: rotate(0deg); }
              100% { transform: rotate(360deg); }
            }
          `}
        </style>
        <SkeletonLoader />
      </div>
    );
  }

  return (
    <div style={{
      minHeight: '100vh',
      background: 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
      position: 'relative',
      width: '100vw',
      margin: 0,
      padding: 0
    }}>
      {/* Header del Dashboard con botón integrado */}
      <div style={{
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        padding: '20px 30px',
        background: 'rgba(255, 255, 255, 0.95)',
        backdropFilter: 'blur(15px)',
        borderBottom: '1px solid rgba(0,0,0,0.08)',
        marginBottom: 20,
        boxShadow: '0 2px 20px rgba(0,0,0,0.05)'
      }}>
        {/* Información del Dashboard */}
        <div style={{ flex: 1, textAlign: 'center' }}>
          <h1 style={{
            fontSize: 28,
            fontWeight: 700,
            color: '#2c3e50',
            marginBottom: 6,
            fontFamily: 'Roboto, sans-serif',
            textShadow: '0 1px 3px rgba(0,0,0,0.1)'
          }}>
            Dashboard SQM - Superintendencia Operaciones Medio Ambiente Nueva Victoria
          </h1>
          <p style={{
            color: '#7f8c8d',
            fontSize: 14,
            margin: 0,
            fontWeight: 500
          }}>
            Grupo {currentGroup + 1} de {groups.length} • Actualización automática cada 30 segundos
          </p>
        </div>
        {/* Botón "Siguiente" integrado en el header */}
        <div style={{ position: 'relative' }}>
          <button
            onClick={() => setCurrentGroup((prev) => (prev + 1) % groups.length)}
            style={{
              background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
              color: '#fff',
              border: 'none',
              borderRadius: 12,
              padding: '12px 20px',
              fontSize: 14,
              fontWeight: 600,
              cursor: 'pointer',
              boxShadow: '0 4px 15px rgba(102, 126, 234, 0.3)',
              transition: 'all 0.3s ease',
              display: 'flex',
              alignItems: 'center',
              gap: 8,
              fontFamily: 'Roboto, sans-serif',
              minWidth: 120
            }}
            onMouseEnter={e => {
              e.target.style.transform = 'translateY(-2px)';
              e.target.style.boxShadow = '0 6px 20px rgba(102, 126, 234, 0.4)';
            }}
            onMouseLeave={e => {
              e.target.style.transform = 'translateY(0)';
              e.target.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.3)';
            }}
          >
            <span>Siguiente</span>
            <span style={{ fontSize: 16 }}>→</span>
          </button>
          {/* Indicador de grupo actual */}
          <div style={{
            position: 'absolute',
            top: -8,
            right: -8,
            background: '#e74c3c',
            color: 'white',
            borderRadius: '50%',
            width: 24,
            height: 24,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontSize: 11,
            fontWeight: 700,
            boxShadow: '0 2px 8px rgba(231, 76, 60, 0.3)'
          }}>
            {currentGroup + 1}
          </div>
        </div>
      </div>

      {/* Contenedor principal de las estaciones - Maximizado */}
      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(2, 1fr)',
          gap: 16,
          padding: '0 16px',
          width: '100%',
          maxWidth: 'none',
          margin: 0,
          boxSizing: 'border-box'
        }}
      >
        {groups[currentGroup].map(station => {
          const viento = getUltimoViento(station.code);
          const so2 = getUltimoSO2(station.code);
          const showForecast = station.showForecast;

          // Obtener valores de las variables adicionales
          const hr = getUltimaHR(station.code);
          const temperatura = getUltimaTemperatura(station.code);
          const pm25 = getUltimaPM25(station.code);

          // Mapeo correcto de clave de estación para pronóstico
          const stationKey = stationKeyMap[station.code];
          const stationForecastData = getForecastDataForStation(stationKey);

          return (
            <div
              key={station.code}
              style={{
                border: '1px solid rgba(255,255,255,0.2)',
                borderRadius: 16,
                padding: 12,
                background: 'rgba(255, 255, 255, 0.95)',
                backdropFilter: 'blur(10px)',
                boxShadow: '0 8px 32px rgba(0,0,0,0.1)',
                width: '100%',
                minHeight: showForecast ? 450 : 375,
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                transition: 'all 0.3s ease',
                position: 'relative',
                overflow: 'hidden',
                boxSizing: 'border-box'
              }}
              onMouseEnter={(e) => {
                e.currentTarget.style.transform = 'translateY(-4px)';
                e.currentTarget.style.boxShadow = '0 12px 40px rgba(0,0,0,0.15)';
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.transform = 'translateY(0)';
                e.currentTarget.style.boxShadow = '0 8px 32px rgba(0,0,0,0.1)';
              }}
            >
              {/* Decoración superior */}
              <div style={{
                position: 'absolute',
                top: 0,
                left: 0,
                right: 0,
                height: 4,
                background: 'linear-gradient(90deg, #3498db, #2ecc71, #f39c12)',
                borderRadius: '16px 16px 0 0'
              }} />

              {/* Título mejorado */}
              <div style={{
                fontWeight: 700,
                marginBottom: 15,
                fontSize: 15,
                textAlign: 'center',
                color: '#2c3e50',
                fontFamily: 'Roboto, sans-serif',
                letterSpacing: '0.5px',
                textShadow: '0 1px 2px rgba(0,0,0,0.1)'
              }}>
                {station.name.toUpperCase()}
              </div>

              {/* Layout horizontal igual a EstacionesDashboard */}
              <div style={{
                display: 'flex',
                flexDirection: 'row',
                justifyContent: 'space-between',
                width: '100%',
                gap: 9,
                alignItems: 'flex-start'
              }}>
                {/* IZQUIERDA: Imagen, flecha, viento, SO2, HR, Temp, PM2.5 */}
                <div style={{
                  width: 165,
                  display: 'flex',
                  flexDirection: 'column',
                  alignItems: 'center',
                  justifyContent: 'flex-start',
                  gap: 12,
                  flexShrink: 0
                }}>
                  {/* Imagen y flecha de viento */}
                  <div style={{
                    position: 'relative',
                    width: 165,
                    height: 120,
                    borderRadius: 9,
                    overflow: 'hidden',
                    boxShadow: '0 4px 16px rgba(0,0,0,0.1)',
                    border: '2px solid rgba(255,255,255,0.8)'
                  }}>
                    <img
                      src={estacionsqmImg}
                      alt="Mapa de la estación"
                      style={{
                        width: '100%',
                        height: '100%',
                        objectFit: 'cover'
                      }}
                    />
                    <div
                      style={{
                        position: 'absolute',
                        top: '50%',
                        left: '50%',
                        width: 45,
                        height: 45,
                        transform: `translate(-50%, -50%) rotate(${viento.direccion}deg)`,
                        color: '#e74c3c',
                        fontSize: 30,
                        pointerEvents: 'none',
                        filter: 'drop-shadow(0 2px 4px rgba(0,0,0,0.3))',
                        transition: 'transform 0.5s ease'
                      }}
                    >⬇️</div>
                    <div style={{
                      position: 'absolute',
                      top: 6,
                      left: 6,
                      background: 'rgba(255,255,255,0.95)',
                      padding: '6px 9px',
                      borderRadius: 6,
                      fontSize: 8,
                      fontWeight: 500,
                      color: '#2c3e50',
                      boxShadow: '0 2px 8px rgba(0,0,0,0.1)',
                      backdropFilter: 'blur(5px)'
                    }}>
                      <div style={{ fontWeight: 600, marginBottom: 2 }}>
                        🌪️ {viento.velocidad} m/s
                      </div>
                      <div style={{ fontSize: 10, color: '#7f8c8d' }}>
                        {viento.timestamp ? new Date(viento.timestamp).toLocaleString('es-ES', {
                          hour: '2-digit',
                          minute: '2-digit',
                          day: '2-digit',
                          month: '2-digit'
                        }) : 'N/A'}
                      </div>
                    </div>
                  </div>
                  {/* Indicadores */}
                  <div style={{
                    background: 'linear-gradient(135deg, #2ecc71, #27ae60)',
                    color: 'white',
                    padding: '9px 15px',
                    borderRadius: 9,
                    textAlign: 'center',
                    fontSize: 12,
                    fontWeight: 600,
                    boxShadow: '0 4px 16px rgba(46, 204, 113, 0.3)',
                    minWidth: 150
                  }}>
                    <div style={{ fontSize: 14, opacity: 0.9, marginBottom: 4 }}>
                      SO₂ (μg/m³)
                    </div>
                    <div style={{ fontSize: 20, fontWeight: 700 }}>
                      {so2 !== null && so2 !== undefined ? so2 : 'N/A'}
                    </div>
                  </div>
                  <div style={{
                    background: 'linear-gradient(135deg, #3498db, #2980b9)',
                    color: 'white',
                    padding: '9px 15px',
                    borderRadius: 9,
                    textAlign: 'center',
                    fontSize: 12,
                    fontWeight: 600,
                    boxShadow: '0 4px 16px rgba(52, 152, 219, 0.3)',
                    minWidth: 150
                  }}>
                    <div style={{ fontSize: 14, opacity: 0.9, marginBottom: 4 }}>
                      💧 HR (%)
                    </div>
                    <div style={{ fontSize: 20, fontWeight: 700 }}>
                      {hr !== null && hr !== undefined ? hr.toFixed(1) : 'N/A'}
                    </div>
                  </div>
                  <div style={{
                    background: 'linear-gradient(135deg, #e67e22, #d35400)',
                    color: 'white',
                    padding: '9px 15px',
                    borderRadius: 9,
                    textAlign: 'center',
                    fontSize: 12,
                    fontWeight: 600,
                    boxShadow: '0 4px 16px rgba(230, 126, 34, 0.3)',
                    minWidth: 150
                  }}>
                    <div style={{ fontSize: 14, opacity: 0.9, marginBottom: 4 }}>
                      🌡️ Temp (°C)
                    </div>
                    <div style={{ fontSize: 20, fontWeight: 700 }}>
                      {temperatura !== null && temperatura !== undefined ? temperatura.toFixed(1) : 'N/A'}
                    </div>
                  </div>
                  <div style={{
                    background: 'linear-gradient(135deg, #9b59b6, #8e44ad)',
                    color: 'white',
                    padding: '9px 15px',
                    borderRadius: 9,
                    textAlign: 'center',
                    fontSize: 12,
                    fontWeight: 600,
                    boxShadow: '0 4px 16px rgba(155, 89, 182, 0.3)',
                    minWidth: 150
                  }}>
                    <div style={{ fontSize: 14, opacity: 0.9, marginBottom: 4 }}>
                      🌫️ PM2.5 (μg/m³)
                    </div>
                    <div style={{ fontSize: 20, fontWeight: 700 }}>
                      {pm25 !== null && pm25 !== undefined ? pm25.toFixed(1) : 'N/A'}
                    </div>
                  </div>
                </div>
                {/* DERECHA: Gráficos - Maximizado */}
                <div style={{
                  flex: 1,
                  display: 'flex',
                  flexDirection: 'column',
                  gap: 12,
                  justifyContent: 'flex-start',
                  minWidth: 0
                }}>
                  {/* Gráfico PM10 con contenedor mejorado */}
                  <div style={{
                    background: 'rgba(255,255,255,0.7)',
                    borderRadius: 12,
                    padding: 16,
                    boxShadow: '0 2px 12px rgba(0,0,0,0.05)',
                    border: '1px solid rgba(255,255,255,0.5)'
                  }}>
                    <div style={{
                      fontSize: 16,
                      fontWeight: 600,
                      color: '#2c3e50',
                      marginBottom: 12,
                      display: 'flex',
                      alignItems: 'center',
                      gap: 8
                    }}>
                      📊 PM10 (μg/m³)
                    </div>
                    <AreaChart
                      title=""
                      width={null}
                      height={150}
                      data={getSeriePM10(station.code)}
                      expectedInterval={10 * 60 * 1000}
                      showNormaAmbiental={true}
                      normaAmbientalValue={130}
                    />
                  </div>
                  {/* Gráfico pronóstico con contenedor mejorado */}
                  {showForecast && (
                    <div style={{
                      background: 'rgba(255,255,255,0.7)',
                      borderRadius: 12,
                      padding: 16,
                      boxShadow: '0 2px 12px rgba(0,0,0,0.05)',
                      border: '1px solid rgba(255,255,255,0.5)'
                    }}>
                      <div style={{
                        fontSize: 16,
                        fontWeight: 600,
                        color: '#2c3e50',
                        marginBottom: 12,
                        display: 'flex',
                        alignItems: 'center',
                        gap: 8
                      }}>
                        🔮 PRONÓSTICO PM10
                      </div>
                      <ForecastChart
                        title=""
                        forecastData={stationForecastData.forecast}
                        realData={stationForecastData.real}
                        rangeData={stationForecastData.range}
                        expectedInterval={10 * 60 * 1000}
                        height={67}
                      />
                    </div>
                  )}
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}

export default SqmGrup1y; 