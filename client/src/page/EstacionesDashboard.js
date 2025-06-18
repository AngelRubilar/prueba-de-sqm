import React, { useEffect, useState } from 'react';
import { pm10Stations } from '../config/stations';
import { fetchPM10Data, fetchSO2Data, fetchVientoData, fetchForecastData } from '../services/api';
import AreaChart from '../components/AreaChart';
import ForecastChart from '../components/ForecastChart';
import SkeletonLoader from '../components/SkeletonLoader';
import mapaHuara from '../assets/estacionsqm.png';

// Mapeo de códigos de estación a nombres usados en forecastData
const stationKeyMap = {
  E6: 'Huara',
  E7: 'Victoria',
  E8: 'Colonia Pintados'
};

function EstacionesDashboard() {
  const [pm10Data, setPm10Data] = useState([]);
  const [so2Data, setSo2Data] = useState([]);
  const [windData, setWindData] = useState([]);
  const [forecastData, setForecastData] = useState({
    'Huara': { forecast: [], real: [], range: [] },
    'Victoria': { forecast: [], real: [], range: [] },
    'Colonia Pintados': { forecast: [], real: [], range: [] }
  });
  const [loading, setLoading] = useState(true);
  const [currentGroup, setCurrentGroup] = useState(0);

  // Función para cargar datos de PM10, SO2 y Viento (cada 5 minutos)
  const cargarDatosPrincipales = async () => {
    try {
      const [pm10, so2, viento] = await Promise.all([
        fetchPM10Data(),
        fetchSO2Data(),
        fetchVientoData()
      ]);
      setPm10Data(pm10);
      setSo2Data(so2);
      setWindData(viento);
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

  useEffect(() => {
    const interval = setInterval(() => {
      setCurrentGroup((prevGroup) => (prevGroup + 1) % 2); // Cambia entre 0 y 1
    }, 30000); // Cambia cada 30 segundos
    return () => clearInterval(interval);
  }, []);

  const getUltimoSO2 = (station) => {
    const datos = so2Data.filter(d => d.station_name === station);
    if (!datos.length) return null;
    return datos[datos.length - 1].valor;
  };

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
    return pm10Data
      .filter(d => d.station_name === station)
      .map(d => [new Date(d.timestamp).getTime(), Number(d.valor) === 0 ? null : Number(d.valor)])
      .filter(point => {
        const [timestamp, value] = point;
        return !isNaN(timestamp) && !isNaN(value) && value !== null && value !== 0;
      });
  };

  // Función para determinar si una estación debe mostrar el pronóstico
  const shouldShowForecast = (station) => {
    const forecastStations = ['E6', 'E7', 'E8'];
    return forecastStations.includes(station);
  };

  // Función para obtener la altura del gráfico PM10 según la estación
  const getPM10ChartHeight = (station) => {
    return shouldShowForecast(station) ? 150 : 300; // 300px para estaciones sin pronóstico
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
  
    // Filtrar, eliminar nulos y ordenar para real
    const realRaw = stationData.real
      .filter(d => d[0] >= twoDaysAgo && d[1] !== null && !isNaN(d[1]))
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

  // Pantalla de carga mejorada con Skeleton
  if (loading) {
    return (
      <div style={{
        minHeight: '100vh',
        background: 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
        padding: '20px 0'
      }}>
        {/* Header con indicador de carga */}
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
            Dashboard de Estaciones
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

        {/* Inyectar animación de spin */}
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

  // Dividir las estaciones en grupos de 4
  const groups = [];
  for (let i = 0; i < pm10Stations.length; i += 4) {
    groups.push(pm10Stations.slice(i, i + 4));
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
            Dashboard de la puta madre
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
            onClick={() => setCurrentGroup((prevGroup) => (prevGroup + 1) % 2)}
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
            onMouseEnter={(e) => {
              e.target.style.transform = 'translateY(-2px)';
              e.target.style.boxShadow = '0 6px 20px rgba(102, 126, 234, 0.4)';
            }}
            onMouseLeave={(e) => {
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
          maxWidth: 'none', // Removido límite de ancho
          margin: 0,
          boxSizing: 'border-box'
        }}
      >
        {groups[currentGroup].map(cfg => {
          const viento = getUltimoViento(cfg.station);
          const so2 = getUltimoSO2(cfg.station);
          const showForecast = shouldShowForecast(cfg.station);

          // Mapeo correcto de clave de estación
          const stationKey = stationKeyMap[cfg.station] || cfg.title;
          const stationForecastData = getForecastDataForStation(stationKey);

          return (
            <div
              key={cfg.station}
              style={{
                border: '1px solid rgba(255,255,255,0.2)',
                borderRadius: 16,
                padding: 16,
                background: 'rgba(255, 255, 255, 0.95)',
                backdropFilter: 'blur(10px)',
                boxShadow: '0 8px 32px rgba(0,0,0,0.1)',
                width: '100%',
                minHeight: showForecast ? 600 : 500,
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
                marginBottom: 20,
                fontSize: 20,
                textAlign: 'center',
                color: '#2c3e50',
                fontFamily: 'Roboto, sans-serif',
                letterSpacing: '0.5px',
                textShadow: '0 1px 2px rgba(0,0,0,0.1)'
              }}>
                ESTACIÓN {cfg.title.toUpperCase()}
              </div>

              {/* Contenedor principal optimizado para máximo ancho */}
              <div style={{
                display: 'flex',
                flexDirection: 'row',
                justifyContent: 'space-between',
                width: '100%',
                gap: 16,
                alignItems: 'flex-start'
              }}>
                {/* IZQUIERDA: Imagen, flecha, viento, SO2 - Compacto */}
                <div style={{
                  width: 260,
                  display: 'flex',
                  flexDirection: 'column',
                  alignItems: 'center',
                  justifyContent: 'flex-start',
                  gap: 12,
                  flexShrink: 0
                }}>
                  {/* Contenedor de imagen compacto */}
                  <div style={{
                    position: 'relative',
                    width: 220,
                    height: 160,
                    borderRadius: 12,
                    overflow: 'hidden',
                    boxShadow: '0 4px 16px rgba(0,0,0,0.1)',
                    border: '2px solid rgba(255,255,255,0.8)'
                  }}>
                    <img
                      src={mapaHuara}
                      alt="Mapa de la estación"
                      style={{
                        width: '100%',
                        height: '100%',
                        objectFit: 'cover'
                      }}
                    />

                    {/* Flecha de dirección del viento mejorada */}
                    <div
                      style={{
                        position: 'absolute',
                        top: '50%',
                        left: '50%',
                        width: 60,
                        height: 60,
                        transform: `translate(-50%, -50%) rotate(${viento.direccion}deg)`,
                        color: '#e74c3c',
                        fontSize: 40,
                        pointerEvents: 'none',
                        filter: 'drop-shadow(0 2px 4px rgba(0,0,0,0.3))',
                        transition: 'transform 0.5s ease'
                      }}
                    >⬇️</div>

                    {/* Información de viento mejorada */}
                    <div style={{
                      position: 'absolute',
                      top: 12,
                      left: 12,
                      background: 'rgba(255,255,255,0.95)',
                      padding: '8px 12px',
                      borderRadius: 8,
                      fontSize: 11,
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

                  {/* Indicador SO2 mejorado */}
                  <div style={{
                    background: 'linear-gradient(135deg, #2ecc71, #27ae60)',
                    color: 'white',
                    padding: '12px 20px',
                    borderRadius: 12,
                    textAlign: 'center',
                    fontSize: 16,
                    fontWeight: 600,
                    boxShadow: '0 4px 16px rgba(46, 204, 113, 0.3)',
                    minWidth: 200
                  }}>
                    <div style={{ fontSize: 14, opacity: 0.9, marginBottom: 4 }}>
                      SO₂ (μg/m³)
                    </div>
                    <div style={{ fontSize: 20, fontWeight: 700 }}>
                      {so2 ?? 'N/A'}
                    </div>
                  </div>
                </div>
                {/* DERECHA: Gráficos - Maximizado */}
                <div style={{
                  flex: 1,
                  display: 'flex',
                  flexDirection: 'column',
                  gap: 16,
                  justifyContent: 'flex-start',
                  minWidth: 0 // Permite que flex funcione correctamente
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
                      width={null} // Permitir que use el ancho del contenedor
                      height={getPM10ChartHeight(cfg.station)}
                      data={getSeriePM10(cfg.station)}
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
                        🔮 PRONÓSTICO SO₂
                      </div>
                      <ForecastChart
                        title=""
                        forecastData={stationForecastData.forecast}
                        realData={stationForecastData.real}
                        rangeData={stationForecastData.range}
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

export default EstacionesDashboard;