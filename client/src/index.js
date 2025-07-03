/* import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';
import './index.css';

// React 18: crear y renderizar con createRoot
const container = document.getElementById('root');
const root = createRoot(container);
root.render(<App />); */
// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals

import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import './config/highchartsConfig';
import PM10View from './page/PM10View';
import WindView from './page/WindView';
import SO2View from './page/SO2View';
import CombinedView from './page/CombinedView'; 
import EstacionesDashboard from './page/EstacionesDashboard';
import SqmGrup1y from './page/sqm_grup1y';
import SqmGrup2 from './page/sqm_grup2';
// Importaciones de las nuevas vistas de grupos
import PM10Grup1View from './page/PM10Grup1View';
import PM10Grup2View from './page/PM10Grup2View';
import SO2Grup1View from './page/SO2Grup1View';
import SO2Grup2View from './page/SO2Grup2View';
import './index.css';

const container = document.getElementById('root');
const root = createRoot(container);

root.render(
  <Router basename="/react">
    <Routes>
      <Route path="/" element={<CombinedView />} /> {/* Nueva ruta por defecto */}
      <Route path="/pm10" element={<PM10View />} />
      <Route path="/viento" element={<WindView />} />
      <Route path="/so2" element={<SO2View />} />
      <Route path="/estaciones" element={<EstacionesDashboard />} />
      <Route path="/sqm_grup1y" element={<SqmGrup1y />} />
      <Route path="/sqm_grup2" element={<SqmGrup2 />} />
      {/* Nuevas rutas para grupos */}
      <Route path="/pm10-grup1" element={<PM10Grup1View />} />
      <Route path="/pm10-grup2" element={<PM10Grup2View />} />
      <Route path="/so2-grup1" element={<SO2Grup1View />} />
      <Route path="/so2-grup2" element={<SO2Grup2View />} />
    </Routes>
  </Router>
);