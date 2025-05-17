// filepath: src/config/highchartsConfig.js
import Highcharts from 'highcharts';

Highcharts.setOptions({
  lang: {
    weekdays: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
    months: [
      'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
    ],
    shortMonths: [
      'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
      'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic',
    ],
    loading: 'Cargando...',
    noData: 'No hay datos para mostrar',
    downloadPNG: 'Descargar como imagen PNG',
    downloadJPEG: 'Descargar como imagen JPEG',
    downloadPDF: 'Descargar como documento PDF',
    downloadSVG: 'Descargar como imagen SVG',
    printChart: 'Imprimir gráfico',
    viewFullscreen: 'Ver en pantalla completa',
    resetZoom: 'Restablecer zoom',
    thousandsSep: '.',
    decimalPoint: ',',
  },
});