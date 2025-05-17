import React, { useState, useEffect } from 'react';
import { stationConfigs as so2Stations } from './config/stations';
import { fetchSO2Data } from './services/api';
import AreaChart from './components/AreaChart';

function SO2Dashboard() {
  const [so2Data, setSo2Data] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Función centralizada para cargar datos de SO2
  const loadData = async () => {
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

  // Ejecutar al montar
  useEffect(() => {
    loadData();
  }, []);

  // Prepara la serie para cada estación
  const getSeriesSO2 = stationId =>
    so2Data
      .filter(item => item.station_name === stationId)
      .map(item => {
        const v = Number(item.valor);
        return [new Date(item.timestamp).getTime(), v === 0 ? null : v];
      });

  if (loading) return <p>Cargando datos SO2…</p>;
  if (error)   return <p>Error: {error}</p>;

  return (
    <div style={{ padding: 20 }}>
      <h1 className="text-4xl font-heading text-gray-800 mb-4">
        Dashboard SO2
      </h1>

      <button
        className="btn bg-purple-600 hover:bg-purple-700 text-white font-heading mb-6"
        onClick={loadData}
      >
        🔄 Refrescar vista
      </button>

      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(3, 1fr)',
          gap: 16,
          marginTop: 20
        }}
      >
        {so2Stations.map(cfg => (
          <AreaChart
            key={cfg.station}
            title={cfg.title}
            data={getSeriesSO2(cfg.station)}
          />
        ))}
      </div>
    </div>
  );
}

export default SO2Dashboard; 