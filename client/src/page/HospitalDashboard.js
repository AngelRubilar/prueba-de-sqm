import React, { useEffect, useState } from 'react';
import StockAreaChart from '../components/StockAreaChart';
import WindRosePolarChart from '../components/WindRosePolarChart';
import SkeletonLoader from '../components/SkeletonLoader';
import { fetchPM10Data, fetchHospitalWindData } from '../services/api';

// Nombre exacto de la estación Hospital en los datos
const HOSPITAL_STATION = 'E5';

function HospitalDashboard() {
  const [pm10Data, setPm10Data] = useState([]);
  const [windData, setWindData] = useState([]);
  const [loading, setLoading] = useState(true);

  // Función para cargar datos de PM10 y viento
  const cargarDatos = async () => {
    setLoading(true);
    try {
      console.log('🔄 Cargando datos de Hospital...');
      const [pm10, viento] = await Promise.all([
        fetchPM10Data(),
        fetchHospitalWindData()
      ]);
      setPm10Data(pm10.filter(d => d.station_name === HOSPITAL_STATION));
      setWindData(viento);
      console.log('✅ Datos cargados correctamente');
    } catch (error) {
      console.error('❌ Error al cargar datos:', error);
    } finally {
      setLoading(false);
    }
  };

  // Cargar datos al montar y cada 2 minutos
  useEffect(() => {
    cargarDatos();
    const interval = setInterval(cargarDatos, 120000); // 2 minutos
    return () => clearInterval(interval);
  }, []);

  // Procesar datos para los gráficos
  const getSeriePM10 = () => {
    const serie = pm10Data
      .map(d => [new Date(d.timestamp).getTime(), Number(d.valor) === 0 ? null : Number(d.valor)])
      .filter(point => {
        const [timestamp, value] = point;
        return !isNaN(timestamp) && !isNaN(value) && value !== null && value !== 0;
      });
    
    console.log('📈 Serie PM10 procesada:', serie.length, 'puntos');
    if (serie.length > 0) {
      console.log('📈 Primer punto PM10:', serie[0]);
    }
    
    return serie;
  };

  const getSerieViento = () => {
    const serie = windData
      .map(d => [new Date(d.timestamp).getTime(), Number(d.velocidad) === 0 ? null : Number(d.velocidad)])
      .filter(point => {
        const [timestamp, value] = point;
        return !isNaN(timestamp) && !isNaN(value) && value !== null && value !== 0;
      });
    
    console.log('🌪️ Serie viento procesada:', serie.length, 'puntos');
    if (serie.length > 0) {
      console.log('🌪️ Primer punto viento:', serie[0]);
    }
    
    return serie;
  };

  const getWindRoseData = () => {
    const data = windData
      .filter(d => d.velocidad !== null && d.direccion !== null && d.velocidad > 0)
      .map(d => ({ 
        velocidad: Number(d.velocidad), 
        direccion: Number(d.direccion) 
      }));
    
    console.log('🌹 Datos rosa de vientos:', data.length, 'puntos');
    if (data.length > 0) {
      console.log('🌹 Primer punto rosa:', data[0]);
    }
    
    return data;
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
            Estación Hospital Maria Elena PDA
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
            Cargando datos de la estación Hospital...
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

  // Layout y estilos similares a EstacionesDashboard
  return (
    <div style={{
      minHeight: '100vh',
      background: 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
      padding: '20px 0',
      width: '100vw',
      margin: 0
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
            Estación Hospital Maria Elena PDA
          </h1>
          <p style={{
            color: '#7f8c8d',
            fontSize: 14,
            margin: 0,
            fontWeight: 500
          }}>
            Monitoreo en tiempo real • Actualización automática cada 2 minutos
          </p>
        </div>

        {/* Botón "Actualizar" integrado en el header */}
        <div style={{ position: 'relative' }}>
          <button
            onClick={cargarDatos}
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
            disabled={loading}
          >
            <span>{loading ? 'Actualizando...' : 'Actualizar'}</span>
            <span style={{ fontSize: 16 }}>🔄</span>
          </button>
        </div>
      </div>

      <div style={{
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'flex-start',
        width: '100%',
        minHeight: 600,
      }}>
        <div style={{
          background: 'rgba(255,255,255,0.97)',
          borderRadius: 18,
          boxShadow: '0 8px 32px rgba(44,62,80,0.10)',
          padding: 32,
          width: '100%',
          maxWidth: 1400,
          display: 'flex',
          flexDirection: 'row',
          gap: 24,
          alignItems: 'flex-start',
        }}>
          {/* Columna izquierda: velocidad del viento y PM10 */}
          <div style={{
            display: 'flex',
            flexDirection: 'column',
            gap: 24,
            width: '65%',
            minWidth: 0
          }}>
            {/* Gráfico de velocidad del viento */}
            <div style={{ width: '100%' }}>
              <StockAreaChart
                title="Velocidad del Viento (m/s)"
                data={getSerieViento()}
                yAxisTitle="Velocidad del Viento (m/s)"
                height={350}
                width={null}
                expectedInterval={10 * 60 * 1000}
                showNormaAmbiental={false}
              />
            </div>
            {/* Gráfico PM10 */}
            <div style={{ width: '100%' }}>
              <StockAreaChart
                title="PM10 (μg/m³)"
                data={getSeriePM10()}
                yAxisTitle="PM10 (μg/m³)"
                height={350}
                width={null}
                expectedInterval={10 * 60 * 1000}
                showNormaAmbiental={true}
                normaAmbientalValue={130}
              />
            </div>
          </div>
          {/* Columna derecha: rosa de los vientos */}
          <div style={{ width: '35%', minWidth: 0, display: 'flex', alignItems: 'flex-start' }}>
            <WindRosePolarChart
              title="Rosa de los Vientos"
              data={getWindRoseData()}
              height={300}
              width={400}
              backgroundColor="#D9D3C7"
            />
          </div>
        </div>
      </div>
    </div>
  );
}

export default HospitalDashboard; 