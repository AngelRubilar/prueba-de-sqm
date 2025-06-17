const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs').promises;

class ForecastScheduler {
  constructor() {
    this.isRunning = false;
    this.lastRun = null;
    this.interval = null;
  }

  // Ejecutar el script Python de pronóstico
  async runForecastScript() {
    if (this.isRunning) {
      console.log('Script de pronóstico ya está ejecutándose, saltando...');
      return;
    }

    this.isRunning = true;
    console.log('Iniciando generación de pronóstico...');

    return new Promise((resolve, reject) => {
      const pythonProcess = spawn('python3', ['getForecast.py'], {
        cwd: path.join(__dirname, '../python')
      });

      let pythonOutput = '';
      let pythonError = '';

      pythonProcess.stdout.on('data', (data) => {
        pythonOutput += data.toString();
        console.log('Python stdout:', data.toString());
      });

      pythonProcess.stderr.on('data', (data) => {
        pythonError += data.toString();
        console.error('Python stderr:', data.toString());
      });

      pythonProcess.on('close', (code) => {
        this.isRunning = false;
        this.lastRun = new Date();
        
        console.log(`Script de pronóstico terminado con código: ${code}`);
        if (code === 0) {
          console.log('Pronóstico generado exitosamente');
          resolve(pythonOutput);
        } else {
          console.error('Error en script de pronóstico:', pythonError);
          reject(new Error(`Python process exited with code ${code}. Error: ${pythonError}`));
        }
      });

      pythonProcess.on('error', (error) => {
        this.isRunning = false;
        console.error('Error ejecutando script Python:', error);
        reject(error);
      });
    });
  }

  // Verificar si necesitamos actualizar el pronóstico
  async needsUpdate() {
    try {
      const parquetPath = path.join(__dirname, '../python/databases/fcst_db.parquet');
      const stats = await fs.stat(parquetPath);
      const fileAge = Date.now() - stats.mtime.getTime();
      const maxAge = 2 * 60 * 60 * 1000; // 2 horas

      return fileAge > maxAge;
    } catch (error) {
      console.log('Archivo de pronóstico no existe, necesitamos generarlo');
      return true;
    }
  }

  // Iniciar el programador
  start() {
    console.log('Iniciando programador de pronóstico...');
    
    // Ejecutar inmediatamente si es necesario
    this.checkAndRun();
    
    // Programar ejecución cada hora
    this.interval = setInterval(() => {
      this.checkAndRun();
    }, 60 * 60 * 1000); // Cada hora
  }

  // Detener el programador
  stop() {
    if (this.interval) {
      clearInterval(this.interval);
      this.interval = null;
      console.log('Programador de pronóstico detenido');
    }
  }

  // Verificar y ejecutar si es necesario
  async checkAndRun() {
    try {
      if (await this.needsUpdate()) {
        console.log('Pronóstico necesita actualización, ejecutando...');
        await this.runForecastScript();
      } else {
        console.log('Pronóstico está actualizado, saltando generación');
      }
    } catch (error) {
      console.error('Error en checkAndRun:', error);
    }
  }

  // Forzar actualización
  async forceUpdate() {
    console.log('Forzando actualización de pronóstico...');
    return await this.runForecastScript();
  }

  // Obtener estado del programador
  getStatus() {
    return {
      isRunning: this.isRunning,
      lastRun: this.lastRun,
      isScheduled: this.interval !== null
    };
  }
}

module.exports = new ForecastScheduler();