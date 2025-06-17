# Usar una imagen base de Node.js
FROM node:18

# Establecer variables de entorno
ENV NODE_ENV=development
ENV PYTHONUNBUFFERED=1

# Instalar Python y dependencias del sistema
RUN apt-get update && apt-get install -y \
    python3-full \
    python3-pip \
    python3-venv \
    && rm -rf /var/lib/apt/lists/*

# Crear y activar un entorno virtual de Python
RUN python3 -m venv /app/venv
ENV PATH="/app/venv/bin:$PATH"

# Crear directorio de trabajo
WORKDIR /app

# Crear directorio para los datos de Python
RUN mkdir -p /app/server/python/databases && \
    chmod 777 /app/server/python/databases

# Copiar package.json y package-lock.json
COPY server/package*.json ./

# Instalar dependencias de Node.js
RUN npm install

# Copiar requirements.txt
COPY server/python/requirements.txt /app/server/python/requirements.txt

# Instalar dependencias de Python en el entorno virtual
RUN pip install --no-cache-dir -r /app/server/python/requirements.txt

# Copiar el código de la aplicación
COPY server/ ./server/
COPY client/ ./client/

# Exponer el puerto
EXPOSE 3000

# Comando para mantener el contenedor corriendo
CMD ["tail", "-f", "/dev/null"]