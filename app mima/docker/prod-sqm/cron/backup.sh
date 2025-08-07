#!/bin/bash

# Variables
BACKUP_DIR=/backup
MYSQL_USER=${MYSQL_USER}
MYSQL_PASSWORD=${MYSQL_PASSWORD}
DATABASES=("mimasubs_fc" "mimasubs_sistema")
DATE=$(date +%Y-%m-%d)

# Crear directorio de respaldo si no existe
mkdir -p ${BACKUP_DIR}

# Respaldar cada base de datos
for DB in "${DATABASES[@]}"; do
  mysqldump -u${MYSQL_USER} -p${MYSQL_PASSWORD} ${DB} > ${BACKUP_DIR}/${DB}_${DATE}.sql
done

echo "Backup completed for databases: ${DATABASES[@]} on ${DATE}"
