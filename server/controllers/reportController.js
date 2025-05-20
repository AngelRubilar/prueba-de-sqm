const nodemailer = require('nodemailer'); // Agregar esta línea al inicio
const logAnalyzer = require('../utils/logAnalyzer');
const apiErrorLogger = require('../utils/apiErrorLogger');

class ReportController {
    async generateReport(req, res) {
        try {
            const { days = 1, sendEmail = false } = req.query;
            console.log('Generando reporte para los últimos', days, 'días');
            
            // Verificar si hay logs
            const hayLogs = apiErrorLogger.checkLogs();
            console.log('¿Hay logs disponibles?', hayLogs);

            const metrics = await logAnalyzer.analyzeLogs(parseInt(days));
            console.log('Métricas generadas:', metrics);
            
            if (sendEmail === 'true') {
                console.log('Enviando reporte por correo...');
                await logAnalyzer.sendEmailReport(metrics);
                return res.json({
                    success: true,
                    message: 'Reporte generado y enviado por correo exitosamente',
                    metrics,
                    debug: {
                        hayLogs,
                        logDir: apiErrorLogger.logDir,
                        currentLogFile: apiErrorLogger.getLogFileName()
                    }
                });
            }

            return res.json({
                success: true,
                metrics,
                debug: {
                    hayLogs,
                    logDir: apiErrorLogger.logDir,
                    currentLogFile: apiErrorLogger.getLogFileName()
                }
            });
        } catch (error) {
            console.error('Error al generar reporte:', error);
            return res.status(500).json({
                success: false,
                error: 'Error al generar el reporte',
                debug: {
                    errorMessage: error.message,
                    logDir: apiErrorLogger.logDir,
                    currentLogFile: apiErrorLogger.getLogFileName()
                }
            });
        }
    }

    async testEmailConfig(req, res) {
        try {
            console.log('Configurando transporte de email...');
            console.log('Host:', process.env.SMTP_HOST);
            console.log('Puerto:', process.env.SMTP_PORT);
            console.log('Usuario:', process.env.SMTP_USER);

            const transporter = nodemailer.createTransport({
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
            });

            console.log('Verificando conexión SMTP...');
            // Verificar la conexión
            await transporter.verify();
            console.log('Conexión SMTP verificada correctamente');
            
            console.log('Enviando email de prueba...');
            // Enviar email de prueba
            const info = await transporter.sendMail({
                from: process.env.SMTP_USER,
                to: process.env.EMAIL_RECIPIENTS,
                subject: 'Prueba de configuración de email',
                text: 'Si recibes este email, la configuración es correcta.'
            });

            console.log('Email enviado exitosamente');
            res.json({
                success: true,
                message: 'Configuración de email correcta',
                messageId: info.messageId
            });
        } catch (error) {
            console.error('Error en prueba de email:', error);
            res.status(500).json({
                success: false,
                error: 'Error en la configuración de email',
                details: error.message,
                config: {
                    host: process.env.SMTP_HOST,
                    port: process.env.SMTP_PORT,
                    user: process.env.SMTP_USER
                }
            });
        }
    }

    // Método para ejecución programada
    async generateAndSendDailyReport() {
        try {
            console.log('Iniciando generación de reporte diario...');
            await logAnalyzer.generateAndSendReport(1);
            console.log('Reporte diario enviado exitosamente');
        } catch (error) {
            console.error('Error al enviar reporte diario:', error);
        }
    }
}

module.exports = new ReportController();