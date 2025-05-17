-- Crear usuarios si no existen
CREATE USER IF NOT EXISTS 'api_writer'@'%' IDENTIFIED BY '25euk3i6Yz3496W';
CREATE USER IF NOT EXISTS 'graphics_reader'@'%' IDENTIFIED BY 'DSQrSsXyPFfQrNb';

-- Asignar permisos
GRANT ALL PRIVILEGES ON datos_api.* TO 'api_writer'@'%';
GRANT SELECT ON datos_api.* TO 'graphics_reader'@'%';

-- Asegurarse de que los permisos se apliquen
FLUSH PRIVILEGES; 