# Bull Queue - Schedulers Distribuidos

## Descripción General

Sistema de schedulers distribuidos usando Bull Queue con Redis como backend. Permite ejecutar tareas programadas de forma confiable en entornos con múltiples instancias del servidor, evitando duplicación de trabajos y garantizando ejecución única por intervalo.

## Ventajas sobre `setInterval`

### Antes (❌ Problemas)
```javascript
// Cada instancia ejecuta el scheduler
setInterval(async () => {
  await ejecutarSerpram();
}, 5 * 60 * 1000);

// Con 3 instancias = 3 ejecuciones simultáneas cada 5 minutos
```

**Problemas:**
- Duplicación de datos en múltiples instancias
- No hay garantía de ejecución si una instancia falla
- No hay visibilidad de éxito/fallo
- Difícil pausar/reanudar schedulers
- No hay retry automático en caso de error

### Ahora (✅ Solución)
```javascript
// Bull Queue con Redis - Solo UNA instancia ejecuta
await initSerpramScheduler();

// Con N instancias = 1 ejecución cada 5 minutos (distribuida)
```

**Beneficios:**
- **Distribución**: Solo una instancia ejecuta cada job
- **Confiabilidad**: Retry automático con backoff exponencial
- **Monitoreo**: Dashboard con estadísticas en tiempo real
- **Control**: Pausar/reanudar schedulers vía API
- **Persistencia**: Jobs persisten en Redis

## Schedulers Implementados

### 1. SERPRAM Scheduler

**Frecuencia:** Cada 5 minutos
**Cola:** `serpram-sync`
**Cron:** `*/5 * * * *`

**Configuración:**
```javascript
{
  attempts: 3,                    // 3 reintentos en caso de error
  backoff: {
    type: 'exponential',
    delay: 5000                   // Espera 5s, 10s, 20s entre reintentos
  },
  removeOnComplete: 50,           // Mantener últimos 50 jobs completados
  removeOnFail: 100              // Mantener últimos 100 jobs fallidos
}
```

**Funciones disponibles:**
- `initSerpramScheduler()` - Inicializar scheduler
- `runSerpramSyncNow()` - Ejecutar manualmente
- `getSerpramStats()` - Obtener estadísticas
- `pauseSerpramScheduler()` - Pausar ejecuciones
- `resumeSerpramScheduler()` - Reanudar ejecuciones

### 2. AYT Scheduler

**Frecuencia:** Cada 1 minuto
**Cola:** `ayt-sync`
**Cron:** `* * * * *`

**Configuración:**
```javascript
{
  attempts: 3,
  backoff: {
    type: 'exponential',
    delay: 3000                   // Espera 3s, 6s, 12s entre reintentos
  },
  removeOnComplete: 100,          // Mantener últimos 100 jobs completados
  removeOnFail: 200              // Mantener últimos 200 jobs fallidos
}
```

**Funciones disponibles:**
- `initAytScheduler()` - Inicializar scheduler
- `runAytSyncNow()` - Ejecutar manualmente
- `getAytStats()` - Obtener estadísticas
- `pauseAytScheduler()` - Pausar ejecuciones
- `resumeAytScheduler()` - Reanudar ejecuciones

## API Endpoints

### Obtener Estadísticas de Todas las Colas
```
GET /api/queues/stats
Authorization: Bearer <token>
```

**Respuesta:**
```json
{
  "success": true,
  "timestamp": "2025-11-17T23:45:00.000Z",
  "queues": {
    "serpram-sync": {
      "name": "serpram-sync",
      "waiting": 0,
      "active": 1,
      "completed": 150,
      "failed": 2,
      "delayed": 0,
      "paused": 0,
      "total": 153
    },
    "ayt-sync": {
      "name": "ayt-sync",
      "waiting": 0,
      "active": 0,
      "completed": 500,
      "failed": 5,
      "delayed": 0,
      "paused": 0,
      "total": 505
    }
  }
}
```

### Obtener Estadísticas de Cola Específica
```
GET /api/queues/serpram-sync/stats
Authorization: Bearer <token>
```

### Obtener Jobs de una Cola
```
GET /api/queues/serpram-sync/jobs?state=failed&start=0&end=10
Authorization: Bearer <token>
```

**Estados disponibles:**
- `waiting` - Esperando ser procesados
- `active` - En ejecución
- `completed` - Completados exitosamente
- `failed` - Fallidos
- `delayed` - Con delay programado

### Pausar Cola
```
POST /api/queues/serpram-sync/pause
Authorization: Bearer <token>
```

**Requiere:** Rol `admin`

### Reanudar Cola
```
POST /api/queues/serpram-sync/resume
Authorization: Bearer <token>
```

**Requiere:** Rol `admin`

### Limpiar Jobs Completados
```
POST /api/queues/serpram-sync/clean/completed
Authorization: Bearer <token>
Content-Type: application/json

{
  "gracePeriod": 3600000  // 1 hora en ms
}
```

**Requiere:** Rol `admin`

### Limpiar Jobs Fallidos
```
POST /api/queues/serpram-sync/clean/failed
Authorization: Bearer <token>
```

**Requiere:** Rol `admin`

### Ejecutar Sincronización SERPRAM Manual
```
POST /api/queues/serpram/run
Authorization: Bearer <token>
```

**Requiere:** Rol `admin`

**Respuesta:**
```json
{
  "success": true,
  "message": "Sincronización SERPRAM iniciada",
  "jobId": "12345",
  "timestamp": "2025-11-17T23:45:00.000Z"
}
```

### Ejecutar Sincronización AYT Manual
```
POST /api/queues/ayt/run
Authorization: Bearer <token>
```

**Requiere:** Rol `admin`

### Health Check de Colas
```
GET /api/queues/health
```

**No requiere autenticación**

**Respuesta:**
```json
{
  "status": "healthy",
  "message": "Todas las colas funcionando normalmente",
  "details": {
    "serpram-sync": {
      "name": "serpram-sync",
      "waiting": 0,
      "active": 0,
      "completed": 150,
      "failed": 2,
      "delayed": 0,
      "paused": 0,
      "total": 152
    }
  },
  "timestamp": "2025-11-17T23:45:00.000Z"
}
```

## Inicialización

### En `app.js`

```javascript
const { initAllSchedulers } = require('./schedulers');

// Al iniciar el servidor
app.listen(PORT, async () => {
  console.log(`Servidor corriendo en puerto ${PORT}`);

  // Inicializar schedulers distribuidos
  await initAllSchedulers();
});
```

### Manejo de Cierre Graceful

```javascript
// Manejar cierre graceful
process.on('SIGTERM', async () => {
  logger.info('SIGTERM recibido, cerrando servidor...');

  // Cerrar todas las colas
  const queueManager = require('./utils/queueManager');
  await queueManager.closeAll();

  process.exit(0);
});
```

## Monitoreo

### Logs Automáticos

Bull Queue registra automáticamente todos los eventos:

```
[info] Scheduler SERPRAM inicializado { queue: 'serpram-sync', cron: '*/5 * * * *' }
[info] Job 12345 agregado a cola serpram-sync
[info] Job 12345 activo en cola serpram-sync
[info] Procesando job 12345 de cola serpram-sync
[info] SERPRAM - consultando desde: 2025-11-17T23:40:00.000Z
[info] Job 12345 completado en cola serpram-sync { duration: 1234 }
```

### Métricas Disponibles

Por cada cola:
- `waiting`: Jobs esperando
- `active`: Jobs en ejecución
- `completed`: Jobs completados
- `failed`: Jobs fallidos
- `delayed`: Jobs con delay
- `paused`: Jobs pausados

### Dashboard Visual (Bull Board)

Para habilitar Bull Board (interfaz web de monitoreo):

```javascript
const { createBullBoard } = require('@bull-board/api');
const { BullAdapter } = require('@bull-board/api/bullAdapter');
const { ExpressAdapter } = require('@bull-board/express');
const basicAuth = require('express-basic-auth');

const serverAdapter = new ExpressAdapter();
serverAdapter.setBasePath('/admin/queues');

createBullBoard({
  queues: [
    new BullAdapter(queueManager.getQueue('serpram-sync')),
    new BullAdapter(queueManager.getQueue('ayt-sync'))
  ],
  serverAdapter
});

// Proteger con autenticación básica
app.use(
  '/admin/queues',
  basicAuth({
    users: { admin: process.env.ADMIN_PASSWORD || 'admin' },
    challenge: true
  }),
  serverAdapter.getRouter()
);
```

Acceder en: `http://localhost:3001/admin/queues`

## Configuración Avanzada

### Ajustar Reintentos

```javascript
const queue = queueManager.createQueue('mi-cola', {
  defaultJobOptions: {
    attempts: 5,              // 5 reintentos
    backoff: {
      type: 'fixed',          // Delay fijo (no exponencial)
      delay: 10000            // 10 segundos
    }
  }
});
```

### Agregar Job con Delay

```javascript
await queueManager.addJob(
  'serpram-sync',
  { type: 'manual' },
  {
    delay: 60000  // Ejecutar en 1 minuto
  }
);
```

### Job con Prioridad

```javascript
await queueManager.addJob(
  'serpram-sync',
  { type: 'urgent' },
  {
    priority: 1  // Mayor prioridad (menor número = mayor prioridad)
  }
);
```

### Expresiones Cron Personalizadas

```javascript
// Cada hora en el minuto 0
'0 * * * *'

// Cada día a las 3:00 AM
'0 3 * * *'

// Cada lunes a las 9:00 AM
'0 9 * * 1'

// Cada 15 minutos
'*/15 * * * *'
```

## Troubleshooting

### Cola no procesa jobs

**Causa**: Redis no disponible o procesador no configurado

**Solución**:
1. Verificar conexión Redis: `redis-cli ping`
2. Verificar que el procesador esté configurado
3. Revisar logs para errores de conexión

### Jobs se quedan en estado "active"

**Causa**: Proceso muerto antes de completar job

**Solución**:
1. Bull automáticamente marca estos jobs como "stalled"
2. Configurar `stalledInterval` y `maxStalledCount`
```javascript
queue.process({ stalledInterval: 30000 }, processor);
```

### Duplicación de jobs recurrentes

**Causa**: Múltiples instancias agregando el mismo job

**Solución**:
- Usar `jobId` fijo para jobs recurrentes
```javascript
await queue.add(data, {
  jobId: 'serpram-recurring',  // Evita duplicados
  repeat: { cron: '*/5 * * * *' }
});
```

### Jobs fallando constantemente

**Causa**: Error en el código del procesador

**Solución**:
1. Revisar logs del job: `GET /api/queues/serpram-sync/jobs?state=failed`
2. Ver `failedReason` en los jobs
3. Corregir código y limpiar jobs fallidos

## Mejores Prácticas

### 1. Idempotencia

Los procesadores deben ser idempotentes (ejecutar múltiples veces produce el mismo resultado):

```javascript
async function processSerpramJob(job) {
  const timestamp = await getLastTimestamp();

  // Solo procesar si hay datos nuevos
  if (isTimestampRecent(timestamp)) {
    const data = await fetchData(timestamp);
    await saveData(data);
  }
}
```

### 2. Timeout en Jobs

```javascript
await queue.add(data, {
  timeout: 30000  // Falla si tarda más de 30s
});
```

### 3. Limpieza Periódica

```javascript
// Limpiar jobs antiguos cada día
setInterval(async () => {
  await queueManager.cleanCompletedJobs('serpram-sync', 24 * 60 * 60 * 1000);
  await queueManager.cleanFailedJobs('serpram-sync', 7 * 24 * 60 * 60 * 1000);
}, 24 * 60 * 60 * 1000);
```

### 4. Progreso en Jobs Largos

```javascript
async function processLongJob(job) {
  await job.progress(0);

  await step1();
  await job.progress(33);

  await step2();
  await job.progress(66);

  await step3();
  await job.progress(100);
}
```

## Migración desde setInterval

### Antes
```javascript
setInterval(async () => {
  await ejecutarSerpram();
}, 5 * 60 * 1000);
```

### Después
```javascript
// En app.js
const { initAllSchedulers } = require('./schedulers');

app.listen(PORT, async () => {
  await initAllSchedulers();
});

// Eliminar setInterval antiguo
```

## Roadmap

### Próximas Mejoras

1. **Bull Board Dashboard**
   - Interfaz visual para monitoreo
   - Gestión de jobs desde UI

2. **Métricas Avanzadas**
   - Integración con Prometheus
   - Grafana dashboards

3. **Alertas**
   - Notificaciones si jobs fallan repetidamente
   - Alertas de colas estancadas

4. **Más Schedulers**
   - Migrar scheduler de Esinfa
   - Migrar scheduler de Sercoamb
   - Scheduler para limpieza de logs

---

**Implementado en**: Fase 2 - Semana 1
**Fecha**: 2025-11-17
**Versión**: 1.0
