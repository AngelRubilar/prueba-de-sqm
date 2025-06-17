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
  const [loadingPM10, setLoadingPM10] = useState(false);
  const [loadingSO2, setLoadingSO2] = useState(false);
  const [loadingViento, setLoadingViento] = useState(false);

  const cargarDatosPM10 = async () => {
    setLoadingPM10(true);
    try {
      console.log('Cargando datos PM10...');
      const pm10 = await fetchPM10Data();
      console.log('Datos PM10 recibidos:', pm10);
      setPm10Data(pm10);
      setError(null);
    } catch (err) {
      console.error('Error al cargar PM10:', err);
      setError(err.message);
    } finally {
      setLoadingPM10(false);
    }
  };

  const cargarDatosSO2 = async () => {
    setLoadingSO2(true);
    try {
      console.log('Cargando datos SO2...');
      const so2 = await fetchSO2Data();
      console.log('Datos SO2 recibidos:', so2);
      setSo2Data(so2);
      setError(null);
    } catch (err) {
      console.error('Error al cargar SO2:', err);
      setError(err.message);
    } finally {
      setLoadingSO2(false);
    }
  };

  const cargarDatosViento = async () => {
    setLoadingViento(true);
    try {
      console.log('Cargando datos de viento...');
      const viento = await fetchVientoData();
      console.log('Datos de viento recibidos:', viento);
      setWindData(viento);
      setError(null);
    } catch (err) {
      console.error('Error al cargar viento:', err);
      setError(err.message);
    } finally {
      setLoadingViento(false);
    }
  };

  const cargarDatosIniciales = async () => {
    setLoading(true);
    try {
      console.log('Cargando datos iniciales...');
      await Promise.all([
        cargarDatosPM10(),
        cargarDatosSO2(),
        cargarDatosViento()
      ]);
      console.log('Datos iniciales cargados correctamente');
    } catch (err) {
      console.error('Error al cargar datos iniciales:', err);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    cargarDatosIniciales();

    // Actualización de viento cada 1 minuto (60000 ms)
    const intervaloViento = setInterval(() => {
      console.log('Actualizando datos de viento automáticamente...');
      cargarDatosViento();
    }, 60000);

    // Actualización de PM10 cada 5 minutos (300000 ms)
    const intervaloPM10 = setInterval(() => {
      console.log('Actualizando datos de PM10 automáticamente...');
      cargarDatosPM10();
    }, 300000);

    // Actualización de SO2 cada 4 minutos (240000 ms)
    const intervaloSO2 = setInterval(() => {
      console.log('Actualizando datos de SO2 automáticamente...');
      cargarDatosSO2();
    }, 240000);

    // Limpieza de todos los intervalos
    return () => {
      clearInterval(intervaloViento);
      clearInterval(intervaloPM10);
      clearInterval(intervaloSO2);
    };
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

  if (loading) return <p>Cargando datos iniciales...</p>;
  if (error) return <p>Error: {error}</p>;

  return (
    <div className="mb-4">
      {/* Sección PM10 */}
      <div className="mb-8">
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem' }}>
          <h2 className="text-2xl font-semibold text-gray-800">Material Particulado (PM10)</h2>
          <button
            className="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"
            onClick={cargarDatosPM10}
            disabled={loadingPM10}>
            {loadingPM10 ? '🔄 Actualizando...' : '🔄 Actualizar PM10'}
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
              height={200}
              data={obtenerSeriePM10(cfg.station)}
            />
          ))}
        </div>
      </div>

      {/* Sección SO2 */}
      <div className="mb-8">
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem' }}>
          <h2 className="text-2xl font-semibold text-green-800">Dióxido de Azufre (SO₂)</h2>
          <button
            className="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-500 dark:focus:ring-green-800"
            onClick={cargarDatosSO2}
            disabled={loadingSO2}>
            {loadingSO2 ? '🔄 Actualizando...' : '🔄 Actualizar SO2'}
          </button>
        </div>
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
              height={200}
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
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem' }}>
          <h2 className="text-2xl font-semibold text-purple-800">Datos de Viento</h2>
          <button
            className="text-purple-700 hover:text-white border border-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-purple-500 dark:text-purple-500 dark:hover:text-white dark:hover:bg-purple-500 dark:focus:ring-purple-800"
            onClick={cargarDatosViento}
            disabled={loadingViento}>
            {loadingViento ? '🔄 Actualizando...' : '🔄 Actualizar Viento'}
          </button>
        </div>
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
              height={200}
              data={obtenerSerieViento(cfg.station)}
            />
          ))}
        </div>
      </div>
    </div>
  );
}

export default CombinedView;