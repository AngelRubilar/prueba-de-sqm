# Stage 0: Instala todas las dependencias del monorepo en la raíz
FROM node:18 AS monorepo-deps
WORKDIR /app
COPY package.json package-lock.json ./ 
RUN npm ci 

# Stage 1: Compila React (frontend)
FROM node:18 AS client-builder
WORKDIR /app/client
# Copia solo el package.json del cliente
COPY client/package.json ./
# Instala las dependencias del cliente
RUN npm install
# Copia el resto de los archivos del cliente
COPY client/ ./
# Ahora ejecuta el build
RUN npm run build 

# Stage 2: Prepara el servidor (backend)
FROM node:18 AS server-builder
WORKDIR /app/server
# Copia los archivos del servidor
COPY server/package.json ./
# Instala las dependencias del servidor (solo producción)
RUN npm install --omit=dev
# Copia solo los archivos de la app server
COPY server/ ./


# Stage 3: Configura la imagen final (server + estáticos)
FROM node:18-slim 
WORKDIR /app

# Instalamos vim y otras herramientas útiles
RUN apt-get update && \
    apt-get install -y vim && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copia los artefactos compilados del cliente
COPY --from=client-builder /app/client/build ./client/build

# Copia los archivos del servidor y sus dependencias
COPY --from=server-builder /app/server ./server
#COPY --from=server-builder /app/server/node_modules ./server/node_modules
COPY --from=server-builder /app/server/.env ./server/.env

# Copia los package.json de la raíz y server
COPY package.json server/package.json ./ 

# Instala las dependencias del servidor
RUN npm install
WORKDIR /app/server


ENV NODE_ENV=production
EXPOSE 3000

# Verificar el contenido del archivo .env y mostrar variables de entorno relevantes
RUN echo "=== Contenido del archivo .env ===" && \
    cat .env && \
    echo "=== Fin del archivo .env ==="

# Comando para iniciar el servidor con logging adicional
CMD ["sh", "-c", "echo '=== Variables de entorno ===' && env | grep -E 'DB_|REDIS_' && echo '=== Iniciando aplicación ===' && npm start --prefix server"]