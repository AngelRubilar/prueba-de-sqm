FROM debian:stable-slim

COPY backup.sh /backup.sh
RUN chmod +x /backup.sh

# Instalar default-mysql-client, bash, tzdata y cron
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    bash \
    tzdata \
    cron \
    nano && \
    apt-get clean

# Copiar crontab file
COPY crontab /etc/cron.d/root

# Ajustar permisos y cargar crontab
RUN chmod 0644 /etc/cron.d/root && crontab /etc/cron.d/root


# Configurar la zona horaria
ENV TZ=America/Santiago
RUN ln -sf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Iniciar cron en primer plano
CMD ["cron", "-f"]
