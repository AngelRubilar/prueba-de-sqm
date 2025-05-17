import React, { useState, useEffect } from 'react';
import { windStations } from '../config/stations'; // Estaciones para viento
import { fetchVientoData } from '../services/api'; // Función para obtener datos de viento
import WindRoseChart from '../components/WindRoseChart'; // Componente para mostrar gráficos de viento

function WindView() {
  const [windData, setWindData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const loadWindData = async () => {
    setLoading(true);
    try {
      const data = await fetchVientoData();
      console.log('Datos de viento:', data); // Verifica los datos recibidos
      setWindData(data);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadWindData();// Cargar datos al montar el componente

    const intervalId = setInterval(() => {
      console.log('Actualizando datos automáticamente...');
      loadWindData(); // Actualizar datos cada minuto
    }, 60000); // 60 segundos

    return () => clearInterval(intervalId); // Limpiar el intervalo al desmontar el componente
  }, []);

  const getSeriesWind = stationId =>
    windData
      .filter(item => item.station_name === stationId)
      .map(item => ({
        timestamp: new Date(item.timestamp).getTime(),
        velocidad: Number(item.velocidad), // Ajusta los nombres de las propiedades según los datos
        direccion: Number(item.direccion),
      }));
   
  if (loading) return <p>Cargando datos de viento…</p>;
  if (error) return <p>Error: {error}</p>;

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h1 className="text-2xl font-semibold text-purple-800 mb-4">
          Dashboard Viento
        </h1>
        <button
          className="text-purple-700 hover:text-white border border-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-purple-500 dark:text-purple-500 dark:hover:text-white dark:hover:bg-purple-500 dark:focus:ring-purple-800"
          onClick={loadWindData}
        >
          🔄 Actualizar datos de viento
        </button>
       </div> 
      <div
        style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(3, 1fr)',
          gap: 16,
          marginTop: 20,
        }}
      >
        {windStations.map(cfg => (
          <WindRoseChart
            key={cfg.station}
            title={cfg.title}
            data={getSeriesWind(cfg.station)}
          />
        ))}
      </div>
    </div>
  );
}

export default WindView;