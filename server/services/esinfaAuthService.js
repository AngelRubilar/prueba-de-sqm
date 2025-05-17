const axios = require('axios');

class EsinfaAuthService {
  constructor() {
    this.token = null;
    this.tokenExpiration = null;
    this.config = {
      baseURL: 'https://airsqm.weboard.cl/login',
      credentials: {
        username: process.env.ESINFA_USER,
        password: process.env.ESINFA_PASS,
        client_name: "esinfa"
      }
    };
  }

  async obtenerNuevoToken() {
    try {
      console.log('=== Iniciando obtención de nuevo token Esinfa ===');
      const data = JSON.stringify(this.config.credentials);

      const config = {
        method: 'post',
        maxBodyLength: Infinity,
        url: this.config.baseURL,
        headers: { 
          'Content-Type': 'application/json'
        },
        data: data
      };

      console.log('Enviando petición para obtener token Esinfa...');
      const response = await axios.request(config);
      
      if (!response.data || !response.data.token) {
        console.error('Respuesta inesperada:', response.data);
        throw new Error('No se pudo obtener el token de la respuesta');
      }

      this.token = response.data.token;
      this.tokenExpiration = new Date(Date.now() + 24 * 60 * 60 * 1000); // 24 horas
      
      console.log('=== Token Esinfa obtenido exitosamente ===');
      console.log('Token:', this.token);
      console.log('Expira:', this.tokenExpiration);
      
      return this.token;
    } catch (error) {
      console.error('=== Error al obtener nuevo token Esinfa ===');
      console.error('Mensaje de error:', error.message);
      if (error.response) {
        console.error('Respuesta del servidor:', error.response.data);
        console.error('Status:', error.response.status);
      }
      throw error;
    }
  }

  async getToken() {
    console.log('=== Verificando token Esinfa actual ===');
    console.log('Token actual:', this.token ? 'Existe' : 'No existe');
    console.log('Expiración:', this.tokenExpiration);
    
    // Si no hay token o está expirado, obtener uno nuevo
    if (!this.token || !this.tokenExpiration || this.tokenExpiration <= new Date()) {
      console.log('Token expirado o no existe, obteniendo nuevo token...');
      await this.obtenerNuevoToken();
    } else {
      console.log('Token válido, no es necesario renovar');
    }
    
    return this.token;
  }
}

module.exports = new EsinfaAuthService();