import React from 'react';
import Highcharts from 'highcharts';
import HighchartsReact from 'highcharts-react-official';

// 1) Importar los módulos de exporting y export-data usando import
import ExportingModule from 'highcharts/modules/exporting';
import ExportDataModule from 'highcharts/modules/export-data';

// 2) Inicializar los módulos pasándolos a la instancia de Highcharts
ExportingModule(Highcharts);
ExportDataModule(Highcharts); 

const ChartWrapper = ({ options }) => {
  // 3) Forzar que el botón de exportar esté activo
  const opts = {
    exporting: { enabled: true },
    ...options
  };

  return <HighchartsReact highcharts={Highcharts} options={opts} />;
};

export default ChartWrapper;
/* import React from 'react';
import Highcharts from 'highcharts';
import HighchartsReact from 'highcharts-react-official';

const ChartWrapper = ({ options }) => (
  <HighchartsReact highcharts={Highcharts} options={options} />
);

export default ChartWrapper; */