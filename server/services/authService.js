const axios = require('axios');

class AuthService {
  constructor() {
    this.token = null;
    this.tokenExpiration = null;
  }

  async obtenerNuevoToken() {
    try {
      const data = JSON.stringify({
       "usuario": "sqm.serpram",
       "password":"S5t2JK!N6%8!"
      });
      const config = {
        method: 'post',
        maxBodyLength: Infinity,
        url: 'https://api.serpram.cl/air_ws/v1/api/login',
        headers: { 'Content-Type': 'application/json' },
        data
      };

      // LOG: antes de hacer la petición
      //console.log('🔐 Obteniendo nuevo token Serpram');
      //console.log('URL:', config.url);
      //console.log('Payload:', data);

      const response = await axios.request(config);

      // LOG: respuesta del login
      //console.log('🔐 Respuesta login Serpram:', response.status, response.data);

      this.token = response.data.value;
      this.tokenExpiration = new Date(Date.now() + 24 * 60 * 60 * 1000);
      return this.token;
    } catch (error) {
      // LOG: detalle del error de login
      console.error('🔐 Error al obtener nuevo token Serpram:', 
        error.response ? error.response.status : '', 
        error.response ? error.response.data : error.message
      );
      throw error;
    }
  }

  async getToken() {
    // Depuración de estado del token
    //console.log(`Token actual: ${this.token || 'No existe'}`);
    //console.log(`Expiración: ${this.tokenExpiration || 'null'}`);

    // Si no hay token o está expirado, obtener uno nuevo
    if (!this.token || !this.tokenExpiration || this.tokenExpiration <= new Date()) {
      //console.log('Token expirado o no existe, obteniendo nuevo token...');
      await this.obtenerNuevoToken();
      //console.log(`Nuevo token obtenido para serpram: ${this.token}`);
    }

    return this.token;
  }
}

module.exports = new AuthService();