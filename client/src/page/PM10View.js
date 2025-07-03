import React, { useState, useEffect } from 'react';
import { pm10Stations } from '../config/stations';
import { fetchPM10Data } from '../services/api';
import AreaChart from '../components/AreaChart';
import RateLimitError from '../components/RateLimitError';
import SkeletonLoader from '../components/SkeletonLoader';

function PM10View() {
  const [pm10Data, setPm10Data] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [retryAfter, setRetryAfter] = useState(null);

  const loadData = async () => {
    setLoading(true);
    try {
      const data = await fetchPM10Data();
      setPm10Data(data);
      setError(null);
      setRetryAfter(null);
    } catch (err) {
      if (err.isRateLimit) {
        setError(err.message);
        setRetryAfter(err.retryAfter);
        // Programar reintento automático
        setTimeout(loadData, err.retryAfter * 1000);
      } else {
        setError(err.message);
      }
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadData();

    const intervalId = setInterval(() => {
      console.log('Actualizando datos automáticamente...');
      loadData();
    }, 60000); // 60 segundos

    return () => clearInterval(intervalId);
  }, []);

  // Ajustar la función para incluir station_name en los datos
  const getSeriesPM10 = (stationId) => {
    const filteredData = pm10Data.filter((item) => item.station_name === stationId);
    return filteredData.map((item) => [
      new Date(item.timestamp).getTime(), // Convertir timestamp a milisegundos
      Number(item.valor) === 0 ? null : Number(item.valor), // Convertir valor a número
    ]);
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
            Dashboard PM10
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
            Cargando datos de PM10...
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

  if (error) {
    if (error.isRateLimit) {
      return <RateLimitError message={error} retryAfter={retryAfter} />;
    }
    return <p>Error: {error}</p>;
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
      {/* Header del Dashboard */}
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
            Dashboard PM10
          </h1>
          <p style={{
            color: '#7f8c8d',
            fontSize: 14,
            margin: 0,
            fontWeight: 500
          }}>
            Monitoreo en tiempo real de PM10 • Actualización automática cada minuto
          </p>
        </div>

        {/* Botón "Actualizar" integrado en el header */}
        <div style={{ position: 'relative' }}>
          <button
            onClick={loadData}
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
              minWidth: 140
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
            <span>🔄 Actualizar</span>
          </button>
        </div>
      </div>

      {/* Contenedor principal de las estaciones - 2 filas x 4 columnas */}
      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(4, 1fr)', // 4 columnas fijas de igual tamaño
          gap: 20,
          padding: '0 20px 20px 20px',
          width: '100%',
          height: 'calc(100vh - 120px)', // Altura calculada para aprovechar toda la vista
          margin: 0,
          boxSizing: 'border-box'
        }}
      >
        {pm10Stations.map((cfg) => {
          const seriesData = getSeriesPM10(cfg.station);

          return (
            <div
              key={cfg.station}
              style={{
                border: '1px solid rgba(255,255,255,0.2)',
                borderRadius: 16,
                padding: 20,
                background: 'rgba(255, 255, 255, 0.95)',
                backdropFilter: 'blur(10px)',
                boxShadow: '0 8px 32px rgba(0,0,0,0.1)',
                width: '100%',
                height: '100%', // Usar toda la altura disponible del grid
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
              {/* Decoración superior con gradiente azul para PM10 */}
              <div style={{
                position: 'absolute',
                top: 0,
                left: 0,
                right: 0,
                height: 4,
                background: 'linear-gradient(90deg, #3498db, #2980b9, #1abc9c)',
                borderRadius: '16px 16px 0 0'
              }} />

              {/* Título mejorado */}
              <div style={{
                fontWeight: 700,
                marginBottom: 16,
                fontSize: 16, // Ligeramente más pequeño para 4 columnas
                textAlign: 'center',
                color: '#2c3e50',
                fontFamily: 'Roboto, sans-serif',
                letterSpacing: '0.5px',
                textShadow: '0 1px 2px rgba(0,0,0,0.1)',
                flexShrink: 0
              }}>
                {cfg.title.toUpperCase()}
              </div>

              {/* Gráfico PM10 con contenedor mejorado */}
              <div style={{
                background: 'rgba(255,255,255,0.7)',
                borderRadius: 12,
                padding: 16,
                boxShadow: '0 2px 12px rgba(0,0,0,0.05)',
                border: '1px solid rgba(255,255,255,0.5)',
                width: '100%',
                flex: 1,
                display: 'flex',
                flexDirection: 'column',
                minHeight: 0
              }}>
                <div style={{
                  fontSize: 14, // Más pequeño para 4 columnas
                  fontWeight: 600,
                  color: '#2c3e50',
                  marginBottom: 12,
                  display: 'flex',
                  alignItems: 'center',
                  gap: 8,
                  flexShrink: 0
                }}>
                  📊 PM10 (μg/m³)
                </div>
                <div style={{ flex: 1, minHeight: 0 }}>
                  <AreaChart
                    title=""
                    data={seriesData}
                    yAxisTitle="PM10 (µg/m³)"
                    height={null} // Permitir que se ajuste automáticamente
                    width={null} // Usar el ancho completo disponible
                    expectedInterval={10 * 60 * 1000} // rango de intervalo esperado de 10 minutos
                    showNormaAmbiental={true}
                    normaAmbientalValue={130}
                    zones={[
                      { value: 130, color: '#15b01a' },
                      { value: 180, color: '#fbfb00' },
                      { value: 230, color: '#ffa400' },
                      { value: 330, color: '#ff0000' },
                      { value: 10000, color: '#8a3d92' },
                    ]}
                  />
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}

export default PM10View;