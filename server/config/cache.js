require('dotenv').config();
const Redis = require('ioredis');

// La URL la pasamos desde REDIS_URL en .env o docker-compose
const redis = new Redis(process.env.REDIS_URL);

// Leer del cache
async function getCache(key) {
  const data = await redis.get(key);
  return data ? JSON.parse(data) : null;
}

// Escribir en el cache con TTL en segundos (default 30s)
async function setCache(key, value, ttl = 30) {
  await redis.set(key, JSON.stringify(value), 'EX', ttl);
}

module.exports = { redis, getCache, setCache };