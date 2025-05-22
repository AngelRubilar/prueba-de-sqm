# docker/development/server.Dockerfile
FROM node:18

# Configuración para evitar advertencias de npm
ENV NPM_CONFIG_FUND=false \
    NPM_CONFIG_UPDATE_NOTIFIER=false \
    NPM_CONFIG_LEGACY_PEER_DEPS=true

# Establece el directorio de trabajo en la raíz del monorepo dentro del contenedor
# Esto es crucial porque las dependencias del monorepo se instalan aquí.
WORKDIR /app

# Copia los package.json de la raíz y de los workspaces (server, client).
# El contexto de construcción en docker-compose.yml (context: ../..)
# se encargará de que estos archivos estén disponibles para ser copiados.
COPY package.json package-lock.json ./
COPY server/package.json ./server/
COPY client/package.json ./client/
 # ¡IMPORTANTE! Asegúrate de incluir el cliente aquí

# Instala todas las dependencias del monorepo en la raíz.
# Esto asegura que todos los módulos estén disponibles centralmente.
RUN npm install

# Exponer el puerto del servidor.
EXPOSE 3000

# El CMD aquí es solo para mantener el contenedor ejecutándose.
# La lógica real de inicio del servidor se manejará en docker-compose.yml.
CMD ["tail", "-f", "/dev/null"]