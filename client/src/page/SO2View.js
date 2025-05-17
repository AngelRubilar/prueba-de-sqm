import React, { useState, useEffect } from 'react';
import { so2Stations } from '../config/stations'; // Configuración de estaciones de SO₂
import { fetchSO2Data } from '../services/api'; // Función para obtener datos de SO₂
import AreaChart from '../components/AreaChart'; // Reutiliza el AreaChart

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

  if (loading) return <p>Cargando datos de SO₂…</p>;
  if (error) return <p>Error: {error}</p>;

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h1 className="text-2xl font-semibold text-green-800 mb-4">
          Dashboard SO₂
        </h1>
        <button
          className="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-500 dark:focus:ring-green-800"
          onClick={loadSO2Data}
        >
          🔄 Actualizar datos SO₂
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
        {so2Stations.map((cfg) => {
          const seriesData = getSeriesSO2(cfg.station);
          console.log(`Datos pasados a AreaChart para ${cfg.title}:`, seriesData);

          return (
            <AreaChart
              key={cfg.station}
              title={cfg.title}
              data={seriesData}
              yAxisTitle="SO₂ (µg/m³)" // Título del eje Y específico para SO₂
              zones={[
                { value: 350, color: "#15b01a" },
                { value: 500, color: "#fbfb00" },
                { value: 650, color: "#ffa400" },
                { value: 950, color: "#ff0000" },
                { value: 10000, color: "#8a3d92" },
              ]} // Zonas específicas para SO₂
            />
          );
        })}
      </div>
    </div>
  );
}

export default SO2View;