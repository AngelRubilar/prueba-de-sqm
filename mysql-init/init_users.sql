CREATE USER 'api_writer'@'%' IDENTIFIED BY '25euk3i6Yz3496W';
GRANT SELECT, INSERT, UPDATE, DELETE ON datos_api.* TO 'api_writer'@'%';

CREATE USER 'graphics_reader'@'%' IDENTIFIED BY 'DSQrSsXyPFfQrNb';
GRANT SELECT ON datos_api.* TO 'graphics_reader'@'%';

FLUSH PRIVILEGES; 