const requestLogger = (req, res, next) => {
    const start = Date.now();
    
    // Registrar la petición
    console.log(`[${new Date().toISOString()}] ${req.method} ${req.originalUrl}`);
    
    // Cuando la respuesta termine
    res.on('finish', () => {
      const duration = Date.now() - start;
      console.log(`[${new Date().toISOString()}] ${req.method} ${req.originalUrl} ${res.statusCode} - ${duration}ms`);
    });
    
    next();
  };
  
  module.exports = requestLogger;