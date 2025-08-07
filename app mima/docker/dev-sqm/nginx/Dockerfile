FROM nginx:latest

# Copiar el script de entrada
COPY docker/dev-sqm/nginx/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Instalar Supervisor para gestionar Nginx y PHP-FPM
RUN apt-get update && apt-get install -y \
    supervisor \
    curl \
    iputils-ping \
    iproute2 \
    vim \
    tzdata \
    && apt-get clean


# Copiar configuración de Nginx
COPY docker/dev-sqm/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/dev-sqm/nginx/default.conf /etc/nginx/conf.d/default.conf


# Copiar configuración de Supervisor
COPY docker/dev-sqm/nginx/supervisor.conf /etc/supervisor/conf.d/supervisor.conf


# Configurar permisos
RUN mkdir -p /var/www/html \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html



# Exponer puertos
EXPOSE 80 443 
# Exponer el puerto 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["/usr/bin/supervisord"]
