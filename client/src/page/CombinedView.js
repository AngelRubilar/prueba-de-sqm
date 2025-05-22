import React, { useState, useEffect } from 'react';
import { pm10Stations, so2Stations, windStations } from '../config/stations';
import { fetchPM10Data, fetchSO2Data, fetchVientoData } from '../services/api';
import AreaChart from '../components/AreaChart';
import WindRoseChart from '../components/WindRoseChart';

function CombinedView() {
  const [pm10Data, setPm10Data] = useState([]);
  const [so2Data, setSo2Data] = useState([]);
  const [windData, setWindData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const cargarTodosLosDatos = async () => {
    setLoading(true);
    try {
      const [pm10, so2, viento] = await Promise.all([
        fetchPM10Data(),
        fetchSO2Data(),
        fetchVientoData()
      ]);
      setPm10Data(pm10);
      setSo2Data(so2);
      setWindData(viento);
      setError(null);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    cargarTodosLosDatos();

    const intervaloId = setInterval(() => {
      console.log('Actualizando datos automáticamente...');
      cargarTodosLosDatos();
    }, 60000);

    return () => clearInterval(intervaloId);
  }, []);

  const obtenerSeriePM10 = (idEstacion) => {
    const datosFiltrados = pm10Data.filter((item) => item.station_name === idEstacion);
    return datosFiltrados.map((item) => [
      new Date(item.timestamp).getTime(),
      Number(item.valor) === 0 ? null : Number(item.valor),
    ]);
  };

  const obtenerSerieSO2 = (idEstacion) => {
    const datosFiltrados = so2Data.filter((item) => item.station_name === idEstacion);
    return datosFiltrados.map((item) => {
      const timestamp = new Date(item.timestamp).getTime();
      const valor = parseFloat(item.valor);
      if (isNaN(timestamp) || isNaN(valor)) {
        return null;
      }
      return [timestamp, valor === 0 ? null : valor];
    }).filter((punto) => punto !== null);
  };

  const obtenerSerieViento = idEstacion =>
    windData
      .filter(item => item.station_name === idEstacion)
      .map(item => ({
        timestamp: new Date(item.timestamp).getTime(),
        velocidad: Number(item.velocidad),
        direccion: Number(item.direccion),
      }));

  if (loading) return <p>Cargando datos...</p>;
  if (error) return <p>Error: {error}</p>;

  return (
    <div className="mb-4">
      {/* Sección PM10 */}
      <div className="mb-8">
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem' }}>
          <h2 className="text-2xl font-semibold text-gray-800">Material Particulado (PM10)</h2>
          <button
            className="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"
            onClick={cargarTodosLosDatos}>
            🔄 Actualizar todos los datos
          </button>
        </div>
        <div style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(8, 1fr)',
          gap: 10,
        }}>
          {pm10Stations.map((cfg) => (
            <AreaChart
              key={cfg.station}
              title={cfg.title}
              width={250}
              height={250}
              data={obtenerSeriePM10(cfg.station)}
            />
          ))}
        </div>
      </div>

      {/* Sección SO2 */}
      <div className="mb-8">
        <h2 className="text-2xl font-semibold text-green-800 mb-4">Dióxido de Azufre (SO₂)</h2>
        <div style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(8, 1fr)',
          gap: 10,
        }}>
          {so2Stations.map((cfg) => (
            <AreaChart
              key={cfg.station}
              title={cfg.title}
              data={obtenerSerieSO2(cfg.station)}
              width={250}
              height={250}
              yAxisTitle="SO₂ (µg/m³)"
              zones={[
                { value: 350, color: "#15b01a" },
                { value: 500, color: "#fbfb00" },
                { value: 650, color: "#ffa400" },
                { value: 950, color: "#ff0000" },
                { value: 10000, color: "#8a3d92" },
              ]}
            />
          ))}
        </div>
      </div>

      {/* Sección Viento */}
      <div>
        <h2 className="text-2xl font-semibold text-purple-800 mb-4">Datos de Viento</h2>
        <div style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(8, 1fr)',
          gap: 10,
        }}>
          {windStations.map(cfg => (
            <WindRoseChart
              key={cfg.station}
              title={cfg.title}
              width={250}
              height={250}
              data={obtenerSerieViento(cfg.station)}
            />
          ))}
        </div>
      </div>
    </div>
  );
}

export default CombinedView;