const moment = require('moment-timezone');
const nodemailer = require('nodemailer');
const path = require('path');
const logProcessor = require('./logProcessor');

class LogAnalyzer {
    constructor() {
        this.logDir = path.join(__dirname, '../logs/api_errors');
        this.emailConfig = {
            host: process.env.SMTP_HOST,
            port: parseInt(process.env.SMTP_PORT),
            secure: false,
            auth: {
                user: process.env.SMTP_USER,
                pass: process.env.SMTP_PASSWORD
            },
            tls: {
                rejectUnauthorized: true
            }
        };
        this.recipients = process.env.EMAIL_RECIPIENTS?.split(',') || [];
        this.verifyEmailConfig();
    }

    verifyEmailConfig() {
        const requiredConfig = [
            'SMTP_HOST',
            'SMTP_PORT',
            'SMTP_USER',
            'SMTP_PASSWORD',
            'EMAIL_RECIPIENTS'
        ];

        const missingConfig = requiredConfig.filter(key => !process.env[key]);
        
        if (missingConfig.length > 0) {
            console.warn('Configuración de email incompleta. Faltan las siguientes variables:');
            missingConfig.forEach(key => console.warn(`- ${key}`));
        } else {
            console.log('Configuración de email completa');
        }
    }

    generateEmailContent(metrics) {
        //const dateRange = `${moment().subtract(1, 'days').format('YYYY-MM-DD')}`;
        const dateRange = `${moment().tz('America/Santiago').subtract(1, 'days').format('DD-MM-YYYY')}`;
        
        return `
            <h2>Reporte de Errores API Correspondientes al día- ${dateRange}</h2>

            <h3>Resumen General</h3>
            <p>Total de errores: ${metrics.totalErrors}</p>

            <h3>Errores por Tipo</h3>
            <table>
            <thead>
                <tr>
                <th>Tipo de Error</th>
                <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                ${Object.entries(metrics.errorsByType)
                .map(([type, count]) => `
                    <tr>
                    <td>${type}</td>
                    <td>${count}</td>
                    </tr>
                `)
                .join('')}
            </tbody>
            </table>

            <h3>Errores por API</h3>
            <table>
            <thead>
                <tr>
                <th>API</th>
                <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                ${Object.entries(metrics.errorsByApi)
                .map(([api, count]) => `
                    <tr>
                    <td>${api}</td>
                    <td>${count}</td>
                    </tr>
                `)
                .join('')}
            </tbody>
            </table>

            <h3>Errores por Estación</h3>
            <table>
            <thead>
                <tr>
                <th>Estación</th>
                <th>Cantidad</th>
                <th>% Error</th>
                <th>% Efectividad</th>
                </tr>
            </thead>
            <tbody>
                ${Object.entries(metrics.errorsByStation)
                .map(([station, count]) => {
                    const porcentajeInfo = metrics.erroresConPorcentaje.find(item => item.Estación === station);
                    const porcentajeError = porcentajeInfo ? porcentajeInfo.PorcentajeError : 0;
                    const porcentajeEfectividad = (100 - porcentajeError).toFixed(2);
                    return `
                    <tr>
                        <td>${station}</td>
                        <td>${count}</td>
                        <td>${porcentajeError}%</td>
                        <td>${porcentajeEfectividad}%</td>
                    </tr>
                    `;
                })
                .join('')}
            </tbody>
            </table>         
                        
            
        `;
    }

    async sendEmailReport(metrics, fecha) {
        if (!this.recipients.length) {
            console.error('No hay destinatarios configurados para el reporte');
            return;
        }

        try {
            const transporter = nodemailer.createTransport(this.emailConfig);
            await transporter.verify();
            console.log('Conexión SMTP verificada correctamente');

            const emailContent = this.generateEmailContent(metrics);
            
            // Rutas de los archivos CSV
            const csvDetalladoPath = path.join(this.logDir, `errores_con_porcentaje_${fecha}.csv`);
            const csvResumenPath = path.join(this.logDir, `resumen_errores_${fecha}.csv`);

            const mailOptions = {
                from: process.env.SMTP_USER,
                to: this.recipients.join(','),
                subject: `Reporte de Errores API - ${fecha}`,
                html: emailContent,
                attachments: [
                    {
                        filename: `errores_con_porcentaje_${fecha}.csv`,
                        path: csvDetalladoPath
                    },
                    {
                        filename: `resumen_errores_${fecha}.csv`,
                        path: csvResumenPath
                    }
                ]
            };

            const info = await transporter.sendMail(mailOptions);
            console.log('Reporte enviado exitosamente');
            console.log('ID del mensaje:', info.messageId);
            return info;
        } catch (error) {
            console.error('Error al enviar el reporte:', error);
            throw error;
        }
    }

    async generateAndSendReport() {
        try {
            const { metrics, fecha } = await logProcessor.procesarLog();
            await this.sendEmailReport(metrics, fecha);
            
            console.log('Proceso completado:');
            console.log('- Reporte general generado y enviado por email');
            console.log('- Archivos CSV adjuntados al correo');
            
        } catch (error) {
            console.error('Error al generar y enviar el reporte:', error);
        }
    }
}

module.exports = new LogAnalyzer();