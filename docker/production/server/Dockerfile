# Stage 0: Instala todas las dependencias del monorepo en la raíz
FROM node:18 AS monorepo-deps
WORKDIR /app
COPY package.json package-lock.json ./ 
RUN npm ci 



# Stage 1: Compila React (frontend)
FROM node:18 AS client-builder
WORKDIR /app/client
# Copia los archivos del cliente (package.json, etc.)
COPY client/package.json ./ 

COPY client/ ./
# Copia las dependencias instaladas en la raíz al contexto de client
COPY --from=monorepo-deps /app/node_modules ./node_modules
# Asegúrate de que npm run build encuentre las dependencias
# Puede que necesites añadir un symlink si la app no encuentra node_modules
# En algunos casos, puede ser necesario un 'npm install --prefix .' o similar, pero la idea es usar lo de la raíz.
RUN npm run build 
# Compila la aplicación React


# Stage 2: Prepara el servidor (backend)
FROM node:18 AS server-builder
WORKDIR /app/server
# Copia los archivos del servidor (package.json, etc.)
COPY server/package.json ./
 # No necesitamos el package-lock.json aquí
# No se ejecuta npm ci aquí, ya que las dependencias están en la raíz
# Copia solo los archivos de la app server
COPY server/ ./
# Copia las dependencias instaladas en la raíz al contexto de server
COPY --from=monorepo-deps /app/node_modules ./node_modules
# Si necesitas quitar dependencias de desarrollo para producción en el server
# Esto es opcional y depende de cómo esté configurado tu server/package.json
# RUN npm ci --omit=dev --prefix . # Usar --prefix . para que sepa dónde está el package.json si node_modules es un symlink



# Stage 3: Configura la imagen final (server + estáticos)
FROM node:18-slim 
WORKDIR /app

# Copia los artefactos compilados del cliente
COPY --from=client-builder /app/client/build ./client/build

# Copia los archivos del servidor
COPY --from=server-builder /app/server ./server

# Copia el node_modules centralizado. Este es el paso clave.
COPY --from=monorepo-deps /app/node_modules ./node_modules

# Opcional: Copiar el package.json de la raíz y el server para scripts de inicio si los necesitas
COPY package.json server/package.json ./ 
# Copia los package.json de la raíz y server

ENV NODE_ENV=production
EXPOSE 3000

# Asegúrate de que el comando de inicio busque el script 'start' en server/package.json
# Puedes navegar a la carpeta server antes de ejecutar si es necesario
# CMD ["node", "server/index.js"] o lo que sea tu archivo de inicio del servidor
CMD ["npm", "start", "--prefix", "server"] # Si tu script 'start' está en server/package.json