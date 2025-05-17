# Stage 1: Base Node image for development
FROM node:18
WORKDIR /app
COPY package*.json ./
COPY server/package*.json ./server/
# Install dependencies in root and server
RUN npm install && cd server && npm install
COPY . .
# Expose the port and set the default command for development
EXPOSE 3000
WORKDIR /app/server
CMD ["npm", "run", "dev"]