const AverageService = require('./averageService');

class AverageScheduler {
    constructor() {
        this.averageService = new AverageService();
        this.isRunning = false;
        this.intervalId = null;
        this.logger = console;
    }

    /**
     * Inicia el scheduler para calcular promedios cada hora
     */
    start() {
        if (this.isRunning) {
            this.logger.warn('AverageScheduler ya está ejecutándose');
            return;
        }

        this.isRunning = true;
        this.logger.info('Iniciando AverageScheduler...');

        // Ejecutar inmediatamente al iniciar
        this.ejecutarCalculoPromedios();

        // Programar ejecución cada hora
        this.intervalId = setInterval(() => {
            this.ejecutarCalculoPromedios();
        }, 60 * 60 * 1000); // 1 hora en milisegundos

        this.logger.info('AverageScheduler iniciado - ejecutándose cada hora');
    }

    /**
     * Detiene el scheduler
     */
    stop() {
        if (!this.isRunning) {
            this.logger.warn('AverageScheduler no está ejecutándose');
            return;
        }

        this.isRunning = false;
        
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }

        this.logger.info('AverageScheduler detenido');
    }

    /**
     * Ejecuta el cálculo de promedios con manejo de errores
     */
    async ejecutarCalculoPromedios() {
        const startTime = Date.now();
        
        try {
            this.logger.info('Ejecutando cálculo de promedios...');
            
            await this.averageService.calcularPromediosDiarios();
            
            const duration = Date.now() - startTime;
            this.logger.info(`Cálculo de promedios completado en ${duration}ms`);
            
        } catch (error) {
            this.logger.error('Error en cálculo de promedios:', error);
            
            // No detener el scheduler por errores, solo loguear
            // El scheduler continuará intentando en la próxima hora
        }
    }

    /**
     * Ejecuta limpieza de datos antiguos (una vez al día)
     */
    async ejecutarLimpieza() {
        try {
            this.logger.info('Ejecutando limpieza de promedios antiguos...');
            await this.averageService.limpiarPromediosAntiguos();
            this.logger.info('Limpieza de promedios completada');
        } catch (error) {
            this.logger.error('Error en limpieza de promedios:', error);
        }
    }

    /**
     * Obtiene el estado del scheduler
     */
    getStatus() {
        return {
            isRunning: this.isRunning,
            lastExecution: this.lastExecution,
            nextExecution: this.getNextExecutionTime()
        };
    }

    /**
     * Calcula la próxima ejecución programada
     */
    getNextExecutionTime() {
        if (!this.isRunning || !this.intervalId) {
            return null;
        }

        const now = new Date();
        const nextHour = new Date(now);
        nextHour.setHours(now.getHours() + 1, 0, 0, 0);
        
        return nextHour;
    }

    /**
     * Ejecuta cálculo manual (para testing o ejecución inmediata)
     */
    async ejecutarCalculoManual() {
        this.logger.info('Ejecutando cálculo manual de promedios...');
        await this.ejecutarCalculoPromedios();
    }
}

module.exports = AverageScheduler; 