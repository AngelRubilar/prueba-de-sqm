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
import CombinedView from './page/CombinedView'; // Nueva importación
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
    </Routes>
  </Router>
);