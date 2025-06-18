import React, { useState, useEffect } from 'react';
import { so2Stations } from '../config/stations'; // Configuración de estaciones de SO₂
import { fetchSO2Data } from '../services/api'; // Función para obtener datos de SO₂
import AreaChart from '../components/AreaChart'; // Reutiliza el AreaChart
import SkeletonLoader from '../components/SkeletonLoader'; // Importar SkeletonLoader

function SO2View() {
  const [so2Data, setSo2Data] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const loadSO2Data = async () => {
    setLoading(true);
    try {
      const data = await fetchSO2Data();
      setSo2Data(data);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadSO2Data();// Cargar datos al montar el componente

    const intervalId = setInterval(() => {
      console.log('Actualizando datos automáticamente...');
      loadSO2Data(); // Actualizar datos cada minuto
    }, 60000); // 60 segundos

    return () => clearInterval(intervalId); // Limpiar el intervalo al desmontar el componente
  }, []);

  const getSeriesSO2 = (stationId) => {
    const filteredData = so2Data.filter((item) => item.station_name === stationId);
    console.log(`Datos filtrados para la estación ${stationId}:`, filteredData);

    return filteredData.map((item) => {
      const timestamp = new Date(item.timestamp).getTime();
      const valor = parseFloat(item.valor); // Convertir el valor a número

      // Validar que el timestamp y el valor sean válidos
      if (isNaN(timestamp) || isNaN(valor)) {
        console.warn(`Datos inválidos para la estación ${stationId}:`, item);
        return null;
      }

      return [timestamp, valor === 0 ? null : valor];
    }).filter((point) => point !== null); // Filtrar puntos inválidos
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
            Dashboard SO₂
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
              border: '2px solid #27ae60',
              borderTop: '2px solid transparent',
              borderRadius: '50%',
              animation: 'spin 1s linear infinite'
            }} />
            Cargando datos de SO₂...
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

  if (error) return <p>Error: {error}</p>;

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
            Dashboard SO₂
          </h1>
          <p style={{
            color: '#7f8c8d',
            fontSize: 14,
            margin: 0,
            fontWeight: 500
          }}>
            Monitoreo en tiempo real de SO₂ • Actualización automática cada minuto
          </p>
        </div>

        {/* Botón "Actualizar" integrado en el header */}
        <div style={{ position: 'relative' }}>
          <button
            onClick={loadSO2Data}
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

      {/* Contenedor principal de las estaciones */}
      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(3, 1fr)', // 3 columnas fijas de igual tamaño
          gap: 20,
          padding: '0 20px 20px 20px', // Agregué padding bottom
          width: '100%',
          height: 'calc(100vh - 120px)', // Altura calculada para aprovechar toda la vista
          margin: 0,
          boxSizing: 'border-box'
        }}
      >
        {so2Stations.map((cfg) => {
          const seriesData = getSeriesSO2(cfg.station);
          console.log(`Datos pasados a AreaChart para ${cfg.title}:`, seriesData);

          return (
            <div
              key={cfg.station}
              style={{
                border: '1px solid rgba(255,255,255,0.2)',
                borderRadius: 16,
                padding: 20, // Reduje un poco el padding para aprovechar más espacio
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
              {/* Decoración superior */}
              <div style={{
                position: 'absolute',
                top: 0,
                left: 0,
                right: 0,
                height: 4,
                background: 'linear-gradient(90deg, #27ae60, #2ecc71, #58d68d)',
                borderRadius: '16px 16px 0 0'
              }} />

              {/* Título mejorado */}
              <div style={{
                fontWeight: 700,
                marginBottom: 16, // Reduje un poco para aprovechar más espacio
                fontSize: 18, // Reduje ligeramente para que quepan mejor
                textAlign: 'center',
                color: '#2c3e50',
                fontFamily: 'Roboto, sans-serif',
                letterSpacing: '0.5px',
                textShadow: '0 1px 2px rgba(0,0,0,0.1)',
                flexShrink: 0 // No se encoge
              }}>
                {cfg.title.toUpperCase()}
              </div>

              {/* Gráfico SO₂ con contenedor mejorado */}
              <div style={{
                background: 'rgba(255,255,255,0.7)',
                borderRadius: 12,
                padding: 16,
                boxShadow: '0 2px 12px rgba(0,0,0,0.05)',
                border: '1px solid rgba(255,255,255,0.5)',
                width: '100%',
                flex: 1, // Toma todo el espacio disponible
                display: 'flex',
                flexDirection: 'column',
                minHeight: 0 // Permite que se encoja si es necesario
              }}>
                <div style={{
                  fontSize: 16,
                  fontWeight: 600,
                  color: '#2c3e50',
                  marginBottom: 12,
                  display: 'flex',
                  alignItems: 'center',
                  gap: 8,
                  flexShrink: 0 // No se encoge
                }}>
                  📊 SO₂ (μg/m³)
                </div>
                <div style={{ flex: 1, minHeight: 0 }}> {/* Contenedor que se ajusta */}
                  <AreaChart
                    title=""
                    data={seriesData}
                    yAxisTitle="SO₂ (µg/m³)"
                    height={null} // Permitir que se ajuste automáticamente
                    width={null} // Usar el ancho completo disponible
                    zones={[
                      { value: 350, color: "#15b01a" },
                      { value: 500, color: "#fbfb00" },
                      { value: 650, color: "#ffa400" },
                      { value: 950, color: "#ff0000" },
                      { value: 10000, color: "#8a3d92" },
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

export default SO2View;