// src/pages/StationDashboard.js
import React, { useState, useEffect } from 'react';
import { fetchVariablesData } from '../services/api';
import StationMultiChart from '../components/StationMultiChart';

function StationDashboard() {
  const [stationData, setStationData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [lastUpdate, setLastUpdate] = useState(null);

  const stationConfigs = [
    { title: "Estación Mejillones", station: "E1" },
    { title: "Estación Sierra Gorda", station: "E2" },
    { title: "Estación Hospital", station: "E5" },
    { title: "Estación Huara", station: "E6" },
    { title: "Estación Victoria", station: "E7" },
    { title: "Estación Colonia Pintados", station: "E8" }
  ];

  const cargarDatos = async () => {
    setLoading(true);
    try {
      const data = await fetchVariablesData();
      setStationData(data);
      setLastUpdate(new Date());
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    cargarDatos();
    
    // Actualización automática cada 5 minutos
    const intervalo = setInterval(cargarDatos, 300000);
    
    return () => clearInterval(intervalo);
  }, []);

  const getStationData = (stationName) => {
    return stationData.filter(item => item.station_name === stationName);
  };

  if (loading) return <p>Cargando datos iniciales...</p>;
  if (error) return <p>Error: {error}</p>;

  return (
    <div className="mb-4">
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem' }}>
        <h2 className="text-2xl font-semibold text-gray-800">Dashboard de Estaciones</h2>
        <button
          className="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2"
          onClick={cargarDatos}
          disabled={loading}>
          {loading ? '🔄 Actualizando...' : '🔄 Actualizar Datos'}
        </button>
      </div>

      <div style={{
        display: 'grid',
        gridTemplateColumns: 'repeat(3, 1fr)',
        gap: 20,
      }}>
        {stationConfigs.map((config) => (
          <StationMultiChart
            key={config.station}
            title={config.title}
            data={getStationData(config.station)}
          />
        ))}
      </div>

      {lastUpdate && (
        <div className="text-sm text-gray-500 mt-4">
          Última actualización: {lastUpdate.toLocaleString()}
        </div>
      )}
    </div>
  );
}

export default StationDashboard;