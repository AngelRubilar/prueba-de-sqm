/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx,html}",   // tus componentes
    "node_modules/flowbite/**/*.js"      // estilos de Flowbite
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Roboto', 'ui-sans-serif', 'system-ui'],      // tu fuente base
        heading: ['Montserrat', 'ui-sans-serif', 'system-ui'] // fuente para títulos
      }
    }
  },
  plugins: [
    require("flowbite/plugin")
  ]
}