import React, { useState, useEffect } from 'react';
import { pm10Stations } from '../config/stations';
import { fetchPM10Data } from '../services/api';
import AreaChart from '../components/AreaChart';

function PM10View() {
  const [pm10Data, setPm10Data] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const loadData = async () => {
    setLoading(true);
    try {
      const data = await fetchPM10Data();
      setPm10Data(data);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadData(); // Cargar datos al montar el componente

    const intervalId = setInterval(() => {
      console.log('Actualizando datos automáticamente...');
      loadData(); // Actualizar datos cada minuto
    }, 60000); // 60 segundos

    return () => clearInterval(intervalId); //Limpiar el intervalo al desmontar el componente
  }, []);

  // Ajustar la función para incluir station_name en los datos
  const getSeriesPM10 = (stationId) => {
    const filteredData = pm10Data.filter((item) => item.station_name === stationId);
    //console.log(`Datos filtrados para la estación ${stationId}:`, filteredData);

    return filteredData.map((item) => [
      new Date(item.timestamp).getTime(), // Convertir timestamp a milisegundos
      Number(item.valor) === 0 ? null : Number(item.valor), // Convertir valor a número
    ]);
  };

  if (loading) return <p>Cargando datos PM10…</p>;
  if (error) return <p>Error: {error}</p>;

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h1 className="text-2xl font-semibold text-gray-800 mb-4">
          Dashboard PM10
        </h1>
        <button
          className="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"
          onClick={loadData}
        >
         🔄 Actualizar datos
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
        {pm10Stations.map((cfg) => {
          const seriesData = getSeriesPM10(cfg.station);
          //console.log(`Datos pasados a AreaChart para ${cfg.title}:`, seriesData);

          return (
            <AreaChart
              key={cfg.station} // Usar station como clave única
              title={cfg.title} // Título de la estación
              data={seriesData} // Pasar los datos filtrados por estación
            />
          );
        })}
      </div>
    </div>
  );
}

export default PM10View;