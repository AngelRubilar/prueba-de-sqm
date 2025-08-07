# Usa la imagen oficial de MySQL 5.7 como base
FROM mysql:5.7

# Copiar los archivos SQL de inicialización al directorio de inicialización de MySQL
COPY 01-init.sql /docker-entrypoint-initdb.d/
COPY 02-sqm_fc.sql /docker-entrypoint-initdb.d/
COPY 03-sqm_sistema.sql /docker-entrypoint-initdb.d/

# Copiar el archivo de configuración personalizado al contenedor
COPY custom_config.cnf /etc/mysql/conf.d/

# Copiar el script de inicialización
COPY init.sh /docker-entrypoint-initdb.d/

# Hacer que el script init.sh sea ejecutable
RUN chmod +x /docker-entrypoint-initdb.d/init.sh

# Desactivar la verificación de firmas GPG y actualizar el sistema e instalar paquetes necesarios
RUN yum update -y --nogpgcheck && \
    yum install -y tzdata nano --nogpgcheck && \
    yum clean all

# Configurar la zona horaria
ENV TZ=America/Santiago
RUN ln -sf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Configurar la zona horaria
ENV TZ=America/Santiago
RUN ln -sf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Exponer el puerto 3306
EXPOSE 3306
