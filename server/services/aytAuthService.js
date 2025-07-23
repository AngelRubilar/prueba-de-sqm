const axios = require('axios');

class AytAuthService {
    constructor() {
        this.token = null;
        this.tokenExpiration = null;
    }

    async obtenerToken() {
        try {
            console.log('🔄 Obteniendo nuevo token de AYT...');
            console.log('Usuario:', process.env.AYT_USER ? 'Configurado' : 'NO CONFIGURADO');
            console.log('Password:', process.env.AYT_PASS ? 'Configurado' : 'NO CONFIGURADO');
            
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
                // Asumir que el token expira en 24 horas (ajustar según la API)
                this.tokenExpiration = new Date(Date.now() + 24 * 60 * 60 * 1000);
                console.log('✅ Token de AYT obtenido exitosamente');
                console.log('Expira:', this.tokenExpiration);
                return this.token;
            } else {
                throw new Error('No se recibió token en la respuesta');
            }
        } catch (error) {
            console.error('❌ Error al obtener token de AYT:', error.message);
            if (error.response) {
                console.error('Status:', error.response.status);
                console.error('Data:', error.response.data);
            }
            throw error;
        }
    }

    async getValidToken() {
        // Si no hay token o está expirado, obtener uno nuevo
        if (!this.token || !this.tokenExpiration || this.tokenExpiration <= new Date()) {
            console.log('🔄 Token expirado o no existe, obteniendo nuevo token...');
            return await this.obtenerToken();
        }
        console.log('✅ Token válido, no es necesario renovar');
        return this.token;
    }

    // Método para forzar renovación de token
    async forceRenewToken() {
        console.log('🔄 Forzando renovación de token AYT...');
        this.token = null;
        this.tokenExpiration = null;
        return await this.obtenerToken();
    }
}

module.exports = new AytAuthService(); 