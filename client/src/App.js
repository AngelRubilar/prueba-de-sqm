import React from 'react';
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import PM10View from './pages/PM10View';
import SO2View from './pages/SO2View';
import WindView from './pages/WindView';
import CombinedView from './pages/CombinedView'; // Nueva importación

function App() {
  return (
    <Router>
      <div style={{ padding: 20 }}>
        <nav>
          <ul style={{ display: 'flex', gap: 20, listStyle: 'none', padding: 0, marginBottom: 20 }}>
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
          </ul>
        </nav>

        <Routes>
          <Route path="/" element={<CombinedView />} /> {/* Nueva ruta por defecto */}
          <Route path="/pm10" element={<PM10View />} />
          <Route path="/so2" element={<SO2View />} />
          <Route path="/viento" element={<WindView />} />
        </Routes>
      </div>
    </Router>
  );
}

export default App;