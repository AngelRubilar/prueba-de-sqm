import React, { useEffect, useState } from 'react';
import { pm10Stations } from '../config/stations';
import { fetchPM10Data, fetchSO2Data, fetchVientoData, fetchForecastData } from '../services/api';
import AreaChart from '../components/AreaChart';
import ForecastChart from '../components/ForecastChart';
import mapaHuara from '../assets/estacionsqm.png';

function EstacionesDashboard() {
  const [pm10Data, setPm10Data] = useState([]);
  const [so2Data, setSo2Data] = useState([]);
  const [windData, setWindData] = useState([]);
  const [forecastData, setForecastData] = useState({
    forecast: [],
    real: [],
    range: []
  });
  const [loading, setLoading] = useState(true);
  const [currentGroup, setCurrentGroup] = useState(0);

  useEffect(() => {
    async function cargarDatos() {
      setLoading(true);
      try {
        const [pm10, so2, viento, pronostico] = await Promise.all([
          fetchPM10Data(),
          fetchSO2Data(),
          fetchVientoData(),
          fetchForecastData()
        ]);
        console.log("datos pronosticos: ", pronostico);
        setPm10Data(pm10);
        setSo2Data(so2);
        setWindData(viento);
        setForecastData(pronostico);
      } catch (error) {
        console.error('Error al cargar datos:', error);
      } finally {
        setLoading(false);
      }
    }
    cargarDatos();

    // Actualizar datos cada 5 minutos
    const interval = setInterval(cargarDatos, 300000);
    return () => clearInterval(interval);
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

  if (loading) return <div>Cargando datos...</div>;

  // Dividir las estaciones en grupos de 4
  const groups = [];
  for (let i = 0; i < pm10Stations.length; i += 4) {
    groups.push(pm10Stations.slice(i, i + 4));
  }

  return (
    <div style={{ position: 'relative' }}>
      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(2, 1fr)',
          gap: 16,
          padding: 16,
        }}
      >
        {groups[currentGroup].map(cfg => {
          const viento = getUltimoViento(cfg.station);
          const so2 = getUltimoSO2(cfg.station);
          const showForecast = shouldShowForecast(cfg.station);
          
          return (
            <div
              key={cfg.station}
              style={{
                border: '1px solid #ccc',
                borderRadius: 8,
                padding: 16,
                background: '#fff',
                boxShadow: '0 2px 8px #0001',
                minWidth: 600,
                minHeight: showForecast ? 550 : 450, // Altura menor para estaciones sin pronóstico
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
              }}
            >
              {/* Título centrado */}
              <div style={{ fontWeight: 'bold', marginBottom: 8, fontSize: 18, textAlign: 'center' }}>
                {cfg.title.toUpperCase()}
              </div>
              <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', width: '100%' }}>
                {/* IZQUIERDA: Imagen, flecha, viento, SO2 */}
                <div style={{ width: 260, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center' }}>
                  <div style={{ position: 'relative', width: 220, height: 160, marginBottom: 8 }}>
                    <img src={mapaHuara} alt="Mapa" style={{ width: '100%', height: '100%', borderRadius: 8 }} />
                    <div
                      style={{
                        position: 'absolute',
                        top: '50%',
                        left: '50%',
                        width: 200,
                        height: 200,
                        transform: `translate(-50%, -50%) rotate(${viento.direccion}deg)`,
                        color: 'red',
                        fontSize: 50,
                        pointerEvents: 'none'
                      }}
                    >⬇️</div>
                    <div style={{
                      position: 'absolute', 
                      top: 8, 
                      left: 8, 
                      background: 'rgba(255,255,255,0.8)', 
                      padding: 4, 
                      borderRadius: 4, 
                      fontSize: 12
                    }}>
                      Velocidad: {viento.velocidad} m/s<br />
                      {viento.timestamp ? new Date(viento.timestamp).toLocaleString() : ''}
                    </div>
                  </div>
                  <div style={{ marginTop: 8, textAlign: 'center', fontSize: 16 }}>
                    SO₂ (<i>μg/m³</i>): <span style={{ color: 'green', fontWeight: 'bold' }}>{so2 ?? 'N/A'}</span>
                  </div>
                </div>
                {/* DERECHA: Gráficos */}
                <div style={{ flex: 1, display: 'flex', flexDirection: 'column', gap: 16, justifyContent: 'center' }}>
                  <div>
                    <AreaChart
                      title="PM10"
                      width={550}
                      height={getPM10ChartHeight(cfg.station)}
                      data={getSeriePM10(cfg.station)}
                    />
                  </div>
                  {showForecast && (
                    <div>
                      <h4 style={{ margin: 0, fontSize: 16 }}>PRONÓSTICO SO₂</h4>
                      <ForecastChart
                        title={`Pronóstico SO₂ - ${cfg.title}`}
                        forecastData={forecastData.forecast.filter(d => d[0] >= Date.now() - 2 * 24 * 60 * 60 * 1000)}
                        realData={forecastData.real.filter(d => d[0] >= Date.now() - 2 * 24 * 60 * 60 * 1000)}
                        rangeData={forecastData.range.filter(d => d[0] >= Date.now() - 2 * 24 * 60 * 60 * 1000)}
                      />
                    </div>
                  )}
                </div>
              </div>
            </div>
          );
        })}
      </div>
      {/* Botón para cambiar manualmente */}
      <button
        onClick={() => setCurrentGroup((prevGroup) => (prevGroup + 1) % 2)}
        style={{
          position: 'absolute',
          bottom: 16,
          right: 16,
          padding: '8px 16px',
          background: '#007bff',
          color: '#fff',
          border: 'none',
          borderRadius: 4,
          cursor: 'pointer',
        }}
      >
        Siguiente
      </button>
    </div>
  );
}

export default EstacionesDashboard;