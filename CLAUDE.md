# Guía Completa del Proyecto SQM - Sistema de Monitoreo Ambiental

## Descripción General

Sistema de monitoreo ambiental en tiempo real para hospitales y estaciones de medición de calidad del aire. Integra datos de múltiples fuentes IoT y APIs externas (MQTT, SERPRAM, AYT, ESINFA, SERCOAMB, Mimasoft) para proporcionar dashboards en tiempo real y reportes de calidad del aire.

## Stack Tecnológico

### Backend
- **Runtime:** Node.js (Express.js)
- **Base de Datos:** MySQL (con pools reader/writer separados)
- **Cache/Estado:** Redis (ioredis)
- **Autenticación:** JWT (jsonwebtoken, bcryptjs)
- **Comunicación IoT:** MQTT (mqtt.js)
- **Logging:** Winston
- **Timezone:** moment-timezone (America/Santiago)

### Frontend
- **Framework:** React
- **Gráficos:** Componentes personalizados (StockAreaChart, WindRosePolarChart)
- **API Client:** Axios

### Infraestructura
- **Containerización:** Docker + Docker Compose
- **Proxy Reverso:** Nginx (en producción)
- **Arquitectura:** Monorepo (API y frontend en mismo servidor)

## Estructura del Proyecto

```
prueba-de-sqm/
├── server/                    # Backend Node.js
│   ├── app.js                # Punto de entrada principal
│   ├── config/               # Configuraciones
│   │   ├── database.js       # Pools MySQL (reader/writer)
│   │   ├── nombreEstaciones.js
│   │   └── nombreVariables.js
│   ├── controllers/          # Controladores HTTP
│   │   ├── authController.js
│   │   ├── measurementController.js
│   │   ├── serpramController.js
│   │   ├── aytController.js
│   │   └── ...
│   ├── services/             # Lógica de negocio
│   │   ├── stateStore.js     # Gestión de estado con Redis
│   │   ├── measurementService.js
│   │   ├── serpramService.js
│   │   ├── aytService.js
│   │   ├── mqttService.js
│   │   └── ...
│   ├── repositories/         # Acceso a datos
│   │   ├── measurementRepository.js
│   │   ├── mqttRepository.js
│   │   └── ...
│   ├── middleware/           # Middlewares Express
│   │   ├── authMiddleware.js # Autenticación JWT
│   │   ├── rateLimiters.js
│   │   └── requestLogger.js
│   ├── routes/               # Definición de rutas
│   │   ├── apiRoutes.js
│   │   └── authRoutes.js
│   ├── errorHandlers/        # Manejadores de errores
│   ├── utils/                # Utilidades
│   ├── store.js              # Wrapper de compatibilidad (Redis)
│   └── .env.example          # Template de variables de entorno
│
├── client/                    # Frontend React
│   └── src/
│       ├── components/
│       ├── page/
│       │   └── HospitalDashboard.js
│       └── services/
│           └── api.js
│
├── docker/                    # Configuraciones Docker
│   └── development/
│       └── mosquitto/
│
├── docker-compose.yml
├── .gitignore
└── Documentación/
    ├── AUTH_JWT_SETUP.md
    ├── REDIS_MIGRATION.md
    ├── CORS_SECURITY_CONFIG.md
    ├── INSTRUCCIONES_ROTACION_TOKEN_SERPRAM.md
    └── INFORME_COMPLETO_AUDITORIA.md
```

## Arquitectura del Sistema

### Flujo de Datos

```
┌─────────────┐
│  Sensores   │
│  IoT (MQTT) │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────────────┐
│  Backend Node.js/Express            │
│  ├─ MQTT Service                    │
│  ├─ Schedulers (SERPRAM, AYT, etc.) │
│  ├─ Controllers → Services → Repos  │
│  └─ StateStore (Redis)              │
└──────┬──────────────────────────────┘
       │
       ├─→ MySQL (Writer/Reader Pools)
       ├─→ Redis (Cache + Estado)
       └─→ APIs Externas (SERPRAM, AYT, etc.)
       │
       ▼
┌─────────────┐
│  Frontend   │
│  React      │
└─────────────┘
```

### Fuentes de Datos

1. **MQTT**: Datos en tiempo real de sensores IoT
2. **SERPRAM**: API de monitoreo ambiental (Mejillones, Sierra Gorda, etc.)
3. **AYT**: Monitoreo de calidad del aire
4. **ESINFA**: Sistema de información ambiental
5. **SERCOAMB**: Servicios ambientales (Tamentica, Victoria)
6. **Mimasoft**: Integración de aplicaciones

## Configuración de Variables de Entorno

### Archivo `.env` (server/)

```env
# ===== APIs EXTERNAS =====
SERPRAM_API_URL=https://api.serpram.cl/air_ws/v1/api
SERPRAM_TOKEN=                 # JWT token de SERPRAM
SERPRAM_USER=
SERPRAM_PASS=

AYT_USER=
AYT_PASS=
AYT_TOKEN=

ESINFA_USER=
ESINFA_PASS=

SERCOAMB_USER_TAMENTICA=
SERCOAMB_PASS_TAMENTICA=
SERCOAMB_USER_VICTORIA=
SERCOAMB_PASS_VICTORIA=

# ===== BASE DE DATOS =====
DB_HOST=localhost
DB_NAME=datos_api
DB_PORT=3306
DB_WRITER_USER=
DB_WRITER_PASSWORD=
DB_READER_USER=
DB_READER_PASSWORD=

# ===== REDIS =====
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=                # Opcional

# ===== AUTENTICACIÓN JWT =====
JWT_SECRET=                    # Generar con: node -e "console.log(require('crypto').randomBytes(64).toString('hex'))"
JWT_EXPIRES_IN=24h
API_USERNAME=admin
API_PASSWORD_HASH=             # Generar con bcrypt

# ===== FRONTEND Y CORS =====
ENABLE_CORS=false              # false en monorepo, true si frontend separado
FRONTEND_URL=http://localhost:3000

# ===== MQTT =====
MQTT_HOST=mqtt
MQTT_PORT=1883
MQTT_USERNAME=
MQTT_PASSWORD=

# ===== EMAIL =====
SMTP_HOST=
SMTP_PORT=
SMTP_USER=
SMTP_PASSWORD=
EMAIL_RECIPIENTS=

# ===== ENTORNO =====
NODE_ENV=development           # development o production
```

## Patrones y Convenciones

### Arquitectura en Capas

```
Routes (HTTP) → Controllers → Services → Repositories → Database/Redis
```

**Importante:**
- Controllers: Manejan HTTP, validación de entrada, respuestas
- Services: Lógica de negocio, transformaciones
- Repositories: Acceso a datos (MySQL, Redis)

### Gestión de Estado

- **Antes (❌):** `store.json` con `fs.writeFileSync()` (bloqueante)
- **Ahora (✅):** Redis con `stateStore.js` (asíncrono, distribuido)

**Uso:**
```javascript
const stateStore = require('./services/stateStore');

// Guardar timestamp
await stateStore.setLastTimestamp('serpram', timestamp);

// Obtener timestamp
const ts = await stateStore.getLastTimestamp('serpram');
```

### Autenticación JWT

**Endpoints públicos:**
- `POST /auth/login` - Obtener token
- `GET /auth/verify` - Verificar token
- `POST /auth/refresh` - Renovar token

**Proteger endpoints:**
```javascript
const { verifyToken, requireRole } = require('../middleware/authMiddleware');

router.get('/admin/config',
  verifyToken,           // Requiere token válido
  requireRole('admin'),  // Requiere rol admin
  controller.getConfig
);
```

### CORS

**Configuración flexible:**
- `ENABLE_CORS=false`: Modo monorepo (sin restricciones)
- `ENABLE_CORS=true`: Valida contra `FRONTEND_URL`

### Timezone

**Siempre usar America/Santiago:**
```javascript
const moment = require('moment-timezone');
const now = moment().tz('America/Santiago');
```

## Convenciones de Commits

### Formato Obligatorio

Este proyecto usa **Conventional Commits** sin emojis ni referencias a herramientas:

```
<type>: <descripción concisa>

[cuerpo opcional con detalles]
```

### Types Permitidos

- `feat:` - Nueva funcionalidad
- `fix:` - Corrección de bugs
- `docs:` - Cambios en documentación
- `refactor:` - Refactorización de código
- `perf:` - Mejoras de performance
- `test:` - Agregar o modificar tests
- `chore:` - Tareas de mantenimiento
- `style:` - Cambios de formato/estilo

### Ejemplos Correctos

```bash
feat: Implementar autenticación JWT para API
fix: Corregir timezone en consultas SERPRAM
docs: Actualizar README con instrucciones de Redis
refactor: Separar lógica de negocio en services
```

### ❌ NO Hacer

- **NO usar emojis** en mensajes de commit
- **NO agregar** "Generated with Claude Code" o similares
- **NO agregar** "Co-Authored-By: Claude"
- **NO usar** prefijos como [feat], [fix] con corchetes (usar dos puntos)

### ✅ Regla General

**Los commits deben ser profesionales, concisos y sin referencias a las herramientas usadas para generarlos.**

## Comandos Importantes

### Desarrollo

```bash
# Backend
cd server
npm install
npm run dev

# Frontend
cd client
npm install
npm start

# Redis (Docker)
docker run -d --name redis -p 6379:6379 redis:7-alpine

# MySQL (Docker Compose)
docker-compose up -d mysql
```

### Testing

```bash
# Autenticación
curl -X POST http://localhost:3001/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password"}'

# Health Check
curl http://localhost:3001/api/health

# Redis
redis-cli
> KEYS state:*
> GET state:serpram:lastTimestamp
```

### Producción

```bash
# Build
npm run build

# Start
NODE_ENV=production npm start

# Logs
tail -f logs/combined.log
```

## Integraciones Externas

### SERPRAM
- **Frecuencia:** Cada 5 minutos
- **Dispositivos:** Mejillones, Sierra Gorda, SQM Baquedano, Maria Elena
- **Timestamp tracking:** Redis `state:serpram:lastTimestamp`
- **Timezone ajustado:** API -1 hora respecto a Chile

### AYT
- **Frecuencia:** Cada 1 minuto
- **Tags por estación:** Configurados en `aytService.js`
- **Token management:** Auto-renovación si 401
- **Sincronización nocturna:** Proceso batch para datos históricos

### MQTT
- **Broker:** mosquitto (Docker)
- **Topics:** Por estación y variable
- **Persistencia:** MySQL + Redis cache
- **Logger:** Winston dedicado

## Mejores Prácticas

### Seguridad
- ✅ **NUNCA** hardcodear credenciales
- ✅ Usar variables de entorno
- ✅ Tokens en `Authorization: Bearer <token>`
- ✅ Passwords hasheados con bcrypt
- ✅ CORS configurado según entorno
- ✅ Headers de seguridad habilitados
- ✅ Rate limiting por endpoint

### Performance
- ✅ Cache en Redis para lecturas frecuentes
- ✅ Pools de conexión MySQL (reader/writer)
- ✅ Operaciones asíncronas (no bloquear event loop)
- ✅ Índices en queries frecuentes
- ✅ Batch inserts cuando sea posible

### Logging
```javascript
const logger = require('./config/logger'); // Winston

logger.info('Mensaje informativo', { metadata });
logger.warn('Advertencia', { details });
logger.error('Error', { error: err.message, stack: err.stack });
```

### Error Handling
```javascript
async function handler(req, res, next) {
  try {
    // Lógica
  } catch (error) {
    next(error); // Pasar al error handler global
  }
}
```

## Problemas Comunes y Soluciones

### Redis no conecta
```bash
# Verificar que Redis está corriendo
redis-cli ping

# Iniciar Redis
docker start redis
```

### JWT_SECRET no configurado
```bash
# Generar secret
node -e "console.log(require('crypto').randomBytes(64).toString('hex'))"

# Agregar a .env
JWT_SECRET=valor_generado
```

### Token SERPRAM expirado
- Contactar administrador de SERPRAM
- Actualizar `SERPRAM_TOKEN` en `.env`
- Reiniciar servidor

### CORS bloqueando requests
- Verificar `ENABLE_CORS` en `.env`
- Para monorepo: `ENABLE_CORS=false`
- Para frontend separado: `ENABLE_CORS=true` + `FRONTEND_URL=https://...`

## Estado Actual del Proyecto

### ✅ Completado (Fase 1 - Semana 1-2)

1. **Seguridad:**
   - Tokens migrados a variables de entorno
   - Autenticación JWT implementada
   - CORS restrictivo configurado
   - Headers de seguridad (7 headers)
   - .gitignore mejorado

2. **Arquitectura:**
   - Store migrado a Redis
   - Escalabilidad horizontal desbloqueada
   - Thread-safe, sin race conditions
   - Fallback a memoria

3. **Documentación:**
   - 5 guías técnicas completas
   - Instrucciones de setup
   - Troubleshooting

### 📋 Pendiente (Próximas Fases)

1. **Resiliencia:**
   - Circuit Breakers en APIs externas
   - Schedulers distribuidos con Bull Queue
   - Health checks comprehensivos

2. **Observabilidad:**
   - Prometheus + Grafana
   - Métricas de aplicación
   - Alertas configuradas

3. **Testing:**
   - Tests unitarios (objetivo 70%+)
   - Tests de integración
   - Tests E2E

4. **Refactorización:**
   - app.js (separar schedulers)
   - AytService (dividir responsabilidades)
   - Migrar a async/await completo

## Referencias Rápidas

### Endpoints Principales

**Autenticación:**
- `POST /auth/login` - Login
- `GET /auth/verify` - Verificar token
- `POST /auth/refresh` - Renovar token

**Datos:**
- `GET /api/datos-PM10` - Datos PM10
- `GET /api/datos-PM25` - Datos PM2.5
- `GET /api/datos-SO2` - Datos SO2
- `GET /api/datos-viento` - Datos de viento
- `GET /api/datos-viento-hospital` - Viento Hospital (E5)
- `GET /api/promedios-hospital-24h` - Promedios 24h Hospital

**Reportes:**
- `GET /api/reportes/logs` - Generar reporte
- `GET /api/reportes/test-email` - Test de email

### Estaciones Principales

- **E5:** Hospital (estación principal de dashboard)
- **Mejillones**
- **Sierra Gorda**
- **SQM Baquedano**
- **Maria Elena**

### Variables Ambientales Monitoreadas

- PM10, PM2.5 (Material particulado)
- SO2 (Dióxido de azufre)
- CO (Monóxido de carbono)
- VV (Velocidad del viento)
- DV (Dirección del viento)
- Temperatura
- Humedad relativa
- Presión barométrica

## Contacto y Soporte

**Repositorio:** https://github.com/AngelRubilar/prueba-de-sqm
**Documentación:** Ver archivos .md en raíz del proyecto
**Issues:** Crear en GitHub

---

**Última actualización:** 2025-11-17
**Versión:** 1.0 (Post Fase 1 - Semana 1-2)
