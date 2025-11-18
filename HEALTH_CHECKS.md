# Health Checks - Sistema de Monitoreo de Salud

## Descripción General

Sistema comprehensivo de health checks para monitorear el estado de todos los componentes críticos de la aplicación. Permite detectar problemas de manera proactiva y facilita la integración con herramientas de monitoreo y orquestación.

## Componentes Monitoreados

### 1. Base de Datos MySQL
- **Writer Pool**: Conexiones de escritura
- **Reader Pool**: Conexiones de lectura
- Métricas: conexiones activas, libres y en cola

### 2. Redis
- Estado de conexión
- Disponibilidad del servicio
- Health check nativo de Redis

### 3. Circuit Breakers
- Estado de cada Circuit Breaker (CLOSED, OPEN, HALF_OPEN)
- Estadísticas de peticiones (fires, successes, failures)
- Tasa de éxito por API

### 4. Recursos del Sistema
- **Memoria**: Uso de heap, RSS, external
- **CPU**: Tiempo de usuario y sistema

### 5. Servidor
- Tiempo de actividad (uptime)
- Versión de Node.js
- Plataforma y PID

## Endpoints Disponibles

### Health Check Básico
```
GET /health
```

Verifica que el servidor está vivo y respondiendo.

**Respuesta (200 OK):**
```json
{
  "status": "healthy",
  "timestamp": "2025-11-17T23:45:00.000Z",
  "uptime": "2h 15m 30s",
  "nodeVersion": "v18.17.0",
  "platform": "linux",
  "pid": 12345
}
```

### Health Check Detallado
```
GET /health/detailed
```

Verifica todos los componentes del sistema.

**Respuesta (200 OK):**
```json
{
  "status": "healthy",
  "uptime": "2h 15m 30s",
  "timestamp": "2025-11-17T23:45:00.000Z",
  "checks": {
    "mysql-writer": {
      "name": "mysql-writer",
      "status": "healthy",
      "message": "MySQL Writer conectado",
      "details": {
        "connections": 10,
        "freeConnections": 8,
        "queuedConnections": 0
      },
      "responseTime": "5ms",
      "timestamp": "2025-11-17T23:45:00.000Z"
    },
    "mysql-reader": { /* ... */ },
    "redis": { /* ... */ },
    "circuit-breakers": { /* ... */ },
    "memory": { /* ... */ },
    "cpu": { /* ... */ }
  }
}
```

### Health Check de Base de Datos
```
GET /health/database
```

Verifica el estado de los pools MySQL.

**Respuesta (200 OK):**
```json
{
  "status": "healthy",
  "timestamp": "2025-11-17T23:45:00.000Z",
  "writer": {
    "status": "healthy",
    "message": "MySQL Writer conectado",
    "details": {
      "connections": 10,
      "freeConnections": 8,
      "queuedConnections": 0
    }
  },
  "reader": {
    "status": "healthy",
    "message": "MySQL Reader conectado",
    "details": {
      "connections": 10,
      "freeConnections": 9,
      "queuedConnections": 0
    }
  }
}
```

### Health Check de Redis
```
GET /health/redis
```

Verifica el estado de la conexión Redis.

**Respuesta (200 OK):**
```json
{
  "status": "healthy",
  "message": "Redis conectado",
  "details": {
    "connected": true,
    "timestamp": "2025-11-17T23:45:00.000Z"
  },
  "timestamp": "2025-11-17T23:45:00.000Z"
}
```

### Health Check de Circuit Breakers
```
GET /health/circuit-breakers
```

Verifica el estado de todos los Circuit Breakers.

**Respuesta (200 OK):**
```json
{
  "status": "healthy",
  "message": "Todos los Circuit Breakers funcionando normalmente",
  "details": {
    "total": 6,
    "open": 0,
    "halfOpen": 0,
    "closed": 6,
    "breakers": [
      {
        "name": "SERPRAM",
        "state": "CLOSED",
        "fires": 150,
        "successes": 148,
        "failures": 2,
        "successRate": "98.67%"
      },
      {
        "name": "AYT",
        "state": "CLOSED",
        "fires": 500,
        "successes": 495,
        "failures": 5,
        "successRate": "99.00%"
      }
      /* ... otros breakers ... */
    ]
  },
  "timestamp": "2025-11-17T23:45:00.000Z"
}
```

### Health Check del Sistema
```
GET /health/system
```

Verifica el uso de memoria y CPU.

**Respuesta (200 OK):**
```json
{
  "status": "healthy",
  "timestamp": "2025-11-17T23:45:00.000Z",
  "memory": {
    "status": "healthy",
    "message": "Uso de memoria normal",
    "details": {
      "heapUsed": "128.45 MB",
      "heapTotal": "256.00 MB",
      "usagePercent": "50.18%",
      "rss": "200.12 MB",
      "external": "2.34 MB"
    }
  },
  "cpu": {
    "status": "healthy",
    "message": "CPU funcionando normalmente",
    "details": {
      "user": "1234.56s",
      "system": "567.89s",
      "uptime": "8150.23s"
    }
  }
}
```

### Health Check de Servicio Específico
```
GET /health/service/:serviceName
```

Verifica un servicio específico por nombre.

**Servicios disponibles:**
- `mysql-writer`
- `mysql-reader`
- `redis`
- `circuit-breakers`
- `memory`
- `cpu`

**Ejemplo:**
```bash
curl http://localhost:3001/health/service/redis
```

### Liveness Probe
```
GET /health/live
```

Indica si la aplicación está viva. Usado por Kubernetes para saber si debe reiniciar el pod.

**Respuesta (200 OK):**
```json
{
  "status": "alive",
  "timestamp": "2025-11-17T23:45:00.000Z"
}
```

### Readiness Probe
```
GET /health/ready
```

Indica si la aplicación está lista para recibir tráfico. Usado por Kubernetes para saber si debe enviar requests al pod.

**Respuesta (200 OK):**
```json
{
  "status": "ready",
  "timestamp": "2025-11-17T23:45:00.000Z",
  "checks": {
    "database": "healthy",
    "redis": "healthy"
  }
}
```

**Respuesta si no está listo (503 Service Unavailable):**
```json
{
  "status": "not ready",
  "timestamp": "2025-11-17T23:45:00.000Z",
  "checks": {
    "database": "unhealthy",
    "redis": "healthy"
  }
}
```

## Estados Posibles

### Status General
- **healthy**: Todos los componentes funcionando correctamente
- **degraded**: Algunos componentes tienen problemas no críticos
- **unhealthy**: Componentes críticos fallando

### Status por Componente
- **healthy**: Componente funcionando normalmente
- **degraded**: Componente con problemas pero aún funcional (ej. memoria alta)
- **unhealthy**: Componente no funcional
- **unknown**: Componente no registrado o no verificado

## Códigos de Estado HTTP

- **200 OK**: Sistema saludable o con degradación menor
- **503 Service Unavailable**: Sistema no saludable o componentes críticos fallando
- **404 Not Found**: Servicio específico no encontrado

## Integración con Kubernetes

### Liveness Probe

```yaml
livenessProbe:
  httpGet:
    path: /health/live
    port: 3001
  initialDelaySeconds: 30
  periodSeconds: 10
  timeoutSeconds: 5
  failureThreshold: 3
```

### Readiness Probe

```yaml
readinessProbe:
  httpGet:
    path: /health/ready
    port: 3001
  initialDelaySeconds: 10
  periodSeconds: 5
  timeoutSeconds: 3
  failureThreshold: 3
```

## Integración con Prometheus

### Métricas Disponibles

El health check puede ser extendido para exponer métricas en formato Prometheus:

```javascript
// Ejemplo de endpoint de métricas
app.get('/metrics', async (req, res) => {
  const health = await healthCheckService.checkAll();

  // Convertir a formato Prometheus
  let metrics = '';

  // Estado general
  metrics += `health_status{status="${health.status}"} 1\n`;

  // Estados individuales
  for (const [name, check] of Object.entries(health.checks)) {
    const statusValue = check.status === 'healthy' ? 1 :
                       check.status === 'degraded' ? 0.5 : 0;
    metrics += `health_check{service="${name}",status="${check.status}"} ${statusValue}\n`;
  }

  res.set('Content-Type', 'text/plain');
  res.send(metrics);
});
```

## Monitoreo y Alertas

### Ejemplos de Scripts de Monitoreo

**Bash script simple:**
```bash
#!/bin/bash
HEALTH_URL="http://localhost:3001/health/detailed"

response=$(curl -s -o /dev/null -w "%{http_code}" $HEALTH_URL)

if [ $response -ne 200 ]; then
  echo "ALERT: Health check failed with status $response"
  # Enviar notificación (email, Slack, etc.)
fi
```

**Node.js con alertas:**
```javascript
const axios = require('axios');

async function checkHealth() {
  try {
    const response = await axios.get('http://localhost:3001/health/detailed');

    if (response.data.status !== 'healthy') {
      console.warn('Sistema degradado o no saludable:', response.data);

      // Verificar componentes específicos
      for (const [name, check] of Object.entries(response.data.checks)) {
        if (check.status === 'unhealthy') {
          console.error(`CRÍTICO: ${name} no saludable - ${check.message}`);
          // Enviar alerta
        }
      }
    }
  } catch (error) {
    console.error('Error al verificar health check:', error.message);
    // Enviar alerta crítica
  }
}

// Ejecutar cada 30 segundos
setInterval(checkHealth, 30000);
```

## Mejores Prácticas

### 1. No Autenticar Health Checks
Los endpoints de health check NO requieren autenticación para facilitar el monitoreo por sistemas externos.

### 2. Respuestas Rápidas
Los health checks deben responder rápidamente (<100ms) para no sobrecargar el sistema.

### 3. Verificar Componentes Críticos
Priorizar la verificación de componentes que realmente afectan la disponibilidad:
- Base de datos (writer)
- Redis (para estado)
- Circuit Breakers principales

### 4. Niveles de Health Check
- **Liveness**: Muy simple y rápido
- **Readiness**: Verifica dependencias críticas
- **Detailed**: Verifica todo el sistema (usar con menos frecuencia)

### 5. Cachear Resultados
Para health checks frecuentes, considerar cachear resultados por 5-10 segundos.

## Troubleshooting

### Health Check Retorna 503

**Causa**: Uno o más componentes críticos no saludables

**Pasos**:
1. Revisar el health check detallado: `GET /health/detailed`
2. Identificar componentes con status `unhealthy`
3. Revisar logs para errores específicos
4. Verificar conectividad (MySQL, Redis)

### Health Check Muy Lento

**Causa**: Algún componente tarda mucho en responder

**Solución**:
1. Usar health checks específicos: `/health/database`, `/health/redis`
2. Implementar timeouts en verificaciones
3. Cachear resultados de health checks

### Memoria Reportada como Degradada

**Causa**: Uso de memoria > 75%

**Solución**:
1. Revisar logs para memory leaks
2. Aumentar heap size: `node --max-old-space-size=4096 app.js`
3. Investigar objetos grandes en memoria

## Roadmap

### Próximas Mejoras

1. **Dashboard de Health**
   - Interfaz visual para ver estado en tiempo real
   - Historial de health checks

2. **Métricas Avanzadas**
   - Latencia de endpoints
   - Tasa de errores
   - Throughput

3. **Alertas Automáticas**
   - Integración con Slack/Email
   - Webhooks configurables
   - Escalamiento de alertas

4. **Health Check de APIs Externas**
   - Verificar disponibilidad de SERPRAM, AYT, etc.
   - Latencia de respuesta

---

**Implementado en**: Fase 2 - Semana 1
**Fecha**: 2025-11-17
**Versión**: 1.0
