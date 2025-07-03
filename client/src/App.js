import React from 'react';
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import PM10View from './page/PM10View';
import SO2View from './page/SO2View';
import WindView from './page/WindView';
import CombinedView from './page/CombinedView'; // Nueva importación
import EstacionesDashboard from './page/EstacionesDashboard';
// Importaciones de las nuevas vistas de grupos
import PM10Grup1View from './page/PM10Grup1View';
import PM10Grup2View from './page/PM10Grup2View';
import SO2Grup1View from './page/SO2Grup1View';
import SO2Grup2View from './page/SO2Grup2View';

function App() {
  return (
    <Router>
      <div style={{ padding: 20 }}>
        <nav>
          <ul style={{ display: 'flex', gap: 20, listStyle: 'none', padding: 0, marginBottom: 20, flexWrap: 'wrap' }}>
            <li>
              <Link to="/" className="text-gray-700 hover:underline">Dashboard Completo</Link>
            </li>
            <li>
              <Link to="/pm10" className="text-blue-700 hover:underline">PM10</Link>
            </li>
            <li>
              <Link to="/so2" className="text-green-700 hover:underline">SO2</Link>
            </li>
            <li>
              <Link to="/viento" className="text-purple-700 hover:underline">Viento</Link>
            </li>
            <li>
              <Link to="/estaciones" className="text-purple-700 hover:underline">Estaciones</Link>
            </li>
            {/* Nuevas rutas para grupos */}
            <li>
              <Link to="/pm10-grup1" className="text-blue-700 hover:underline">PM10 Grupo 1</Link>
            </li>
            <li>
              <Link to="/pm10-grup2" className="text-blue-700 hover:underline">PM10 Grupo 2</Link>
            </li>
            <li>
              <Link to="/so2-grup1" className="text-green-700 hover:underline">SO2 Grupo 1</Link>
            </li>
            <li>
              <Link to="/so2-grup2" className="text-green-700 hover:underline">SO2 Grupo 2</Link>
            </li>
          </ul>
        </nav>

        <Routes>
          <Route path="/" element={<CombinedView />} /> {/* Nueva ruta por defecto */}
          <Route path="/pm10" element={<PM10View />} />
          <Route path="/so2" element={<SO2View />} />
          <Route path="/viento" element={<WindView />} />
          <Route path="/estaciones" element={<EstacionesDashboard />} />
          {/* Nuevas rutas para grupos */}
          <Route path="/pm10-grup1" element={<PM10Grup1View />} />
          <Route path="/pm10-grup2" element={<PM10Grup2View />} />
          <Route path="/so2-grup1" element={<SO2Grup1View />} />
          <Route path="/so2-grup2" element={<SO2Grup2View />} />
        </Routes>
      </div>
    </Router>
  );
}

export default App;