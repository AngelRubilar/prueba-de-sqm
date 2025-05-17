const axios = require('axios');

class AytAuthService {
    constructor() {
        this.token = null;
    }

    async obtenerToken() {
        try {
            const response = await axios.post('http://104.41.40.103:8080/api/Auth/Login', {
                usuario: process.env.AYT_USER,
                password: process.env.AYT_PASS
            }, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json;charset=UTF-8'
                }
            });

            if (response.data && response.data.authenticationToken) {
                this.token = response.data.authenticationToken;
                return this.token;
            } else {
                throw new Error('No se recibió token en la respuesta');
            }
        } catch (error) {
            console.error('Error al obtener token de AYT:', error.message);
            throw error;
        }
    }

    async getValidToken() {
        if (!this.token) {
            return await this.obtenerToken();
        }
        return this.token;
    }
}

module.exports = new AytAuthService(); 