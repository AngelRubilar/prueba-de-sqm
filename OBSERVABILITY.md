# Observabilidad - Prometheus y Grafana

## Descripción General

Sistema de observabilidad completo usando Prometheus para recolección de métricas y Grafana para visualización. Proporciona visibilidad en tiempo real del rendimiento, salud y comportamiento de la aplicación.

## Arquitectura de Observabilidad

```
┌─────────────────┐
│   Aplicación    │
│   Node.js       │
│                 │
│ /metrics        │◄────┐
│ /metrics/json   │     │
└─────────────────┘     │
                        │ Scraping cada 15s
                   ┌────┴────────┐
                   │ Prometheus  │
                   │   :9090     │
                   └────┬────────┘
                        │
                        │ Consultas PromQL
                   ┌────▼────────┐
                   │  Grafana    │
                   │   :3030     │
                   └─────────────┘
```

## Métricas Disponibles

### 1. Métricas HTTP

#### Contador de Requests
```
sqm_http_requests_total{method, route, status_code}
```
Cuenta el total de requests HTTP por método, ruta y código de estado.

**Ejemplo de uso:**
```promql
# Tasa de requests por segundo
rate(sqm_http_requests_total[5m])

# Requests fallidos (5xx)
sum(rate(sqm_http_requests_total{status_code=~"5.."}[5m]))

# Requests por ruta
sum by (route) (rate(sqm_http_requests_total[5m]))
```

#### Duración de Requests
```
sqm_http_request_duration_seconds{method, route, status_code}
```
Histograma de duración de requests HTTP en segundos.

**Percentiles:**
```promql
# Latencia p50
histogram_quantile(0.50, rate(sqm_http_request_duration_seconds_bucket[5m]))

# Latencia p95
histogram_quantile(0.95, rate(sqm_http_request_duration_seconds_bucket[5m]))

# Latencia p99
histogram_quantile(0.99, rate(sqm_http_request_duration_seconds_bucket[5m]))
```

#### Requests en Progreso
```
sqm_http_requests_in_progress{method, route}
```
Número de requests HTTP actualmente en ejecución.

### 2. Métricas de APIs Externas

#### Llamadas a APIs
```
sqm_external_api_calls_total{api, status}
```
Total de llamadas a APIs externas (SERPRAM, AYT, ESINFA, SERCOAMB).

**Ejemplos:**
```promql
# Tasa de llamadas exitosas
rate(sqm_external_api_calls_total{status="success"}[5m])

# Tasa de errores por API
rate(sqm_external_api_calls_total{status="error"}[5m])

# Tasa de error general
sum(rate(sqm_external_api_calls_total{status="error"}[5m]))
  / sum(rate(sqm_external_api_calls_total[5m])) * 100
```

#### Duración de Llamadas
```
sqm_external_api_duration_seconds{api}
```
Histograma de duración de llamadas a APIs externas.

### 3. Métricas de Circuit Breakers

#### Estado del Circuit Breaker
```
sqm_circuit_breaker_state{circuit}
```
Estado actual del Circuit Breaker:
- 0 = CLOSED (funcionando normal)
- 1 = HALF_OPEN (probando recuperación)
- 2 = OPEN (fallando, usando fallback)

**Alertas recomendadas:**
```promql
# Circuit Breaker abierto por más de 5 minutos
sqm_circuit_breaker_state == 2

# Circuit Breaker cambiando de estado frecuentemente
rate(sqm_circuit_breaker_events_total[5m]) > 0.5
```

#### Tasa de Éxito
```
sqm_circuit_breaker_success_rate{circuit}
```
Porcentaje de éxito (0-100) del Circuit Breaker.

**Alertas:**
```promql
# Tasa de éxito menor a 90%
sqm_circuit_breaker_success_rate < 90
```

#### Eventos
```
sqm_circuit_breaker_events_total{circuit, event}
```
Total de eventos del Circuit Breaker por tipo:
- `success` - Ejecuciones exitosas
- `failure` - Fallos
- `timeout` - Timeouts
- `fallback` - Fallbacks ejecutados
- `open` - Circuito abierto
- `halfOpen` - Circuito medio abierto

### 4. Métricas de Colas Bull

#### Jobs por Estado
```
sqm_queue_jobs{queue, state}
```
Número de jobs en cada estado:
- `waiting` - Esperando procesamiento
- `active` - En ejecución
- `completed` - Completados
- `failed` - Fallidos
- `delayed` - Con delay programado

**Alertas:**
```promql
# Muchos jobs fallando
sqm_queue_jobs{state="failed"} > 10

# Cola acumulando trabajos
sqm_queue_jobs{state="waiting"} > 100

# Jobs activos estancados
sqm_queue_jobs{state="active"} > 5
```

#### Jobs Procesados
```
sqm_queue_jobs_processed_total{queue, status}
```
Total de jobs procesados exitosamente o con error.

#### Duración de Jobs
```
sqm_queue_job_duration_seconds{queue}
```
Histograma de duración de procesamiento de jobs.

### 5. Métricas de Base de Datos

#### Queries Ejecutadas
```
sqm_db_queries_total{pool, status}
```
Total de queries ejecutadas en pool reader/writer.

**Ejemplos:**
```promql
# Tasa de queries por segundo
rate(sqm_db_queries_total[5m])

# Queries fallidas
rate(sqm_db_queries_total{status="error"}[5m])
```

#### Duración de Queries
```
sqm_db_query_duration_seconds{pool}
```
Histograma de duración de queries en segundos.

**Queries lentas:**
```promql
# p95 de duración de queries
histogram_quantile(0.95, rate(sqm_db_query_duration_seconds_bucket[5m]))
```

#### Conexiones de Base de Datos
```
sqm_db_connections{pool, state}
```
Número de conexiones por estado:
- `total` - Total de conexiones
- `free` - Conexiones libres
- `queued` - Queries esperando conexión

**Alertas:**
```promql
# Pool saturado
sqm_db_connections{state="free"} == 0

# Queries en cola
sqm_db_connections{state="queued"} > 0
```

### 6. Métricas de Redis

#### Operaciones Redis
```
sqm_redis_operations_total{operation, status}
```
Total de operaciones Redis (get, set, del, etc.).

#### Duración de Operaciones
```
sqm_redis_operation_duration_seconds{operation}
```
Histograma de duración de operaciones Redis.

### 7. Métricas de Negocio

#### Registros Insertados
```
sqm_data_records_inserted_total{station, source}
```
Total de registros de datos insertados por estación y fuente.

**Ejemplos:**
```promql
# Tasa de inserción por fuente
rate(sqm_data_records_inserted_total[5m])

# Total insertado en 24h
increase(sqm_data_records_inserted_total[24h])
```

#### Última Sincronización Exitosa
```
sqm_last_successful_sync_timestamp{source}
```
Timestamp UNIX de la última sincronización exitosa.

**Alertas:**
```promql
# Sin sincronización en 10 minutos
time() - sqm_last_successful_sync_timestamp > 600
```

### 8. Métricas por Defecto de Node.js

Prometheus recolecta automáticamente métricas del runtime de Node.js:

- `sqm_nodejs_heap_size_total_bytes` - Tamaño total del heap
- `sqm_nodejs_heap_size_used_bytes` - Heap utilizado
- `sqm_nodejs_external_memory_bytes` - Memoria externa
- `sqm_nodejs_eventloop_lag_seconds` - Lag del event loop
- `sqm_process_cpu_user_seconds_total` - CPU en modo usuario
- `sqm_process_cpu_system_seconds_total` - CPU en modo sistema
- `sqm_process_resident_memory_bytes` - Memoria RSS

## Endpoints de Métricas

### Formato Prometheus
```
GET /metrics
Content-Type: text/plain; version=0.0.4; charset=utf-8
```

Endpoint para scraping de Prometheus. Retorna métricas en formato de texto OpenMetrics.

**Ejemplo de salida:**
```
# HELP sqm_http_requests_total Total de requests HTTP recibidas
# TYPE sqm_http_requests_total counter
sqm_http_requests_total{method="GET",route="/api/datos-PM10",status_code="200"} 1523

# HELP sqm_circuit_breaker_state Estado del Circuit Breaker
# TYPE sqm_circuit_breaker_state gauge
sqm_circuit_breaker_state{circuit="SERPRAM"} 0
```

### Formato JSON
```
GET /metrics/json
Content-Type: application/json
```

Endpoint para debugging y visualización humana.

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "timestamp": "2025-11-17T23:45:00.000Z",
  "metrics": [
    {
      "name": "sqm_http_requests_total",
      "type": "counter",
      "help": "Total de requests HTTP recibidas",
      "values": [
        {
          "labels": {
            "method": "GET",
            "route": "/api/datos-PM10",
            "status_code": "200"
          },
          "value": 1523
        }
      ]
    }
  ]
}
```

## Configuración de Prometheus

### Archivo prometheus.yml

```yaml
global:
  scrape_interval: 15s
  evaluation_interval: 15s
  external_labels:
    monitor: 'sqm-monitor'
    environment: 'production'

scrape_configs:
  - job_name: 'sqm-api'
    static_configs:
      - targets: ['host.docker.internal:3000']
        labels:
          application: 'sqm-api'
    metrics_path: '/metrics'
    scrape_interval: 15s
```

### Iniciar Stack de Monitoreo

```bash
cd docker/monitoring
docker-compose -f docker-compose.monitoring.yml up -d
```

**Servicios iniciados:**
- Prometheus: http://localhost:9090
- Grafana: http://localhost:3030
- Node Exporter: http://localhost:9100

### Acceder a Grafana

**URL:** http://localhost:3030

**Credenciales por defecto:**
- Usuario: `admin`
- Contraseña: `admin123`

**Cambiar contraseña:**
```bash
docker exec -it sqm-grafana grafana-cli admin reset-admin-password <nueva_contraseña>
```

## Dashboards de Grafana

### Dashboard Principal: SQM Overview

Dashboard pre-configurado con paneles para:

1. **Tasa de Requests HTTP** - Line chart con tasa de requests por ruta
2. **Latencia HTTP (p95)** - Gauge con percentil 95 de latencia
3. **Estado de Circuit Breakers** - Time series con estados
4. **Jobs en Colas Bull** - Time series con jobs por estado
5. **Conexiones MySQL** - Time series con conexiones por pool
6. **Tasa de Éxito APIs Externas** - Gauge con porcentajes de éxito

**Acceso:** Grafana → Dashboards → SQM - Overview

### Crear Dashboard Personalizado

1. Ir a Grafana → Dashboards → New Dashboard
2. Agregar Panel
3. Seleccionar datasource: Prometheus
4. Escribir query PromQL
5. Configurar visualización
6. Guardar dashboard

**Ejemplo de query:**
```promql
# Tasa de requests por minuto
sum(rate(sqm_http_requests_total[1m])) * 60
```

## Queries Útiles de PromQL

### Performance de la API

```promql
# Throughput total (req/s)
sum(rate(sqm_http_requests_total[5m]))

# Latencia promedio
rate(sqm_http_request_duration_seconds_sum[5m])
  / rate(sqm_http_request_duration_seconds_count[5m])

# Error rate (%)
sum(rate(sqm_http_requests_total{status_code=~"5.."}[5m]))
  / sum(rate(sqm_http_requests_total[5m])) * 100
```

### Salud de Circuit Breakers

```promql
# Circuit Breakers abiertos
count(sqm_circuit_breaker_state == 2)

# Tasa de fallback
rate(sqm_circuit_breaker_events_total{event="fallback"}[5m])

# Success rate promedio
avg(sqm_circuit_breaker_success_rate)
```

### Performance de Colas

```promql
# Jobs completados por minuto
sum(rate(sqm_queue_jobs_processed_total{status="success"}[1m])) * 60

# Jobs fallados por hora
sum(increase(sqm_queue_jobs_processed_total{status="error"}[1h]))

# Tiempo promedio de procesamiento
rate(sqm_queue_job_duration_seconds_sum[5m])
  / rate(sqm_queue_job_duration_seconds_count[5m])
```

### Base de Datos

```promql
# Queries por segundo
sum(rate(sqm_db_queries_total[5m]))

# Pool utilization (%)
(sqm_db_connections{state="total"} - sqm_db_connections{state="free"})
  / sqm_db_connections{state="total"} * 100

# Queries lentas (>100ms)
histogram_quantile(0.95, rate(sqm_db_query_duration_seconds_bucket[5m])) > 0.1
```

### Uso de Recursos

```promql
# Memoria heap usada (MB)
sqm_nodejs_heap_size_used_bytes / 1024 / 1024

# CPU usage (%)
rate(sqm_process_cpu_user_seconds_total[5m]) * 100

# Event loop lag (ms)
sqm_nodejs_eventloop_lag_seconds * 1000
```

## Alertas Recomendadas

### Alta Tasa de Errores HTTP
```yaml
- alert: HighHTTPErrorRate
  expr: |
    sum(rate(sqm_http_requests_total{status_code=~"5.."}[5m]))
      / sum(rate(sqm_http_requests_total[5m])) > 0.05
  for: 5m
  annotations:
    summary: "Alta tasa de errores HTTP (> 5%)"
```

### Circuit Breaker Abierto
```yaml
- alert: CircuitBreakerOpen
  expr: sqm_circuit_breaker_state == 2
  for: 5m
  annotations:
    summary: "Circuit Breaker {{ $labels.circuit }} está OPEN"
```

### Cola de Jobs Acumulándose
```yaml
- alert: QueueBacklog
  expr: sqm_queue_jobs{state="waiting"} > 100
  for: 10m
  annotations:
    summary: "Cola {{ $labels.queue }} tiene {{ $value }} jobs esperando"
```

### Pool de Base de Datos Saturado
```yaml
- alert: DatabasePoolExhausted
  expr: sqm_db_connections{state="free"} == 0
  for: 1m
  annotations:
    summary: "Pool {{ $labels.pool }} sin conexiones libres"
```

### Sin Sincronización Reciente
```yaml
- alert: NoRecentSync
  expr: time() - sqm_last_successful_sync_timestamp > 600
  for: 0m
  annotations:
    summary: "Sin sincronización de {{ $labels.source }} en 10 minutos"
```

## Integración con Código

### Registrar Métrica Personalizada

```javascript
const { dataRecordsInserted } = require('./utils/metrics');

// En tu código de inserción
async function insertarDatos(estacion, fuente, registros) {
  // ... lógica de inserción ...

  // Registrar métrica
  dataRecordsInserted.labels(estacion, fuente).inc(registros.length);
}
```

### Medir Duración de Operación

```javascript
const { dbQueryDuration } = require('./utils/metrics');

async function ejecutarQuery(pool, query) {
  const end = dbQueryDuration.labels(pool).startTimer();

  try {
    const result = await pool.query(query);
    return result;
  } finally {
    end(); // Registra duración automáticamente
  }
}
```

### Actualizar Gauge

```javascript
const { circuitBreakerState } = require('./utils/metrics');

function actualizarEstadoCircuit(nombreCircuit, estado) {
  const valorEstado = estado === 'CLOSED' ? 0 :
                      estado === 'HALF_OPEN' ? 1 : 2;

  circuitBreakerState.labels(nombreCircuit).set(valorEstado);
}
```

## Colector de Métricas

El servicio `metricsCollector` recolecta métricas periódicamente cada 15 segundos:

### Métricas Recolectadas

1. **Circuit Breakers** - Estado y estadísticas de todos los servicios
2. **Colas Bull** - Estadísticas de todas las colas
3. **Base de Datos** - Conexiones de pools reader/writer

### Inicialización

```javascript
// En app.js
const metricsCollector = require('./services/metricsCollector');

app.listen(PORT, () => {
  metricsCollector.start();
  console.log('Colector de métricas iniciado');
});

// Detener al cerrar
process.on('SIGTERM', () => {
  metricsCollector.stop();
});
```

### Configuración

```javascript
// metricsCollector.js
class MetricsCollector {
  constructor() {
    this.collectionInterval = 15000; // 15 segundos
  }

  async collect() {
    await Promise.all([
      this.collectCircuitBreakerMetrics(),
      this.collectQueueMetrics(),
      this.collectDatabaseMetrics()
    ]);
  }
}
```

## Mejores Prácticas

### 1. Nombrar Métricas

Usar prefijo consistente (`sqm_`) y seguir convenciones:
- Contadores: `_total` al final (ej: `sqm_http_requests_total`)
- Gauges: Sin sufijo (ej: `sqm_circuit_breaker_state`)
- Histogramas: `_seconds` o unidad relevante (ej: `sqm_http_request_duration_seconds`)

### 2. Labels Apropiados

- Usar labels para dimensiones (method, route, status)
- NO usar labels con alta cardinalidad (user_id, request_id)
- Mantener número de labels bajo (< 10 por métrica)

### 3. Buckets de Histogramas

Configurar buckets según el rango esperado:

```javascript
// Para latencias HTTP (ms a segundos)
buckets: [0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1, 2.5, 5, 10]

// Para duración de jobs (segundos a minutos)
buckets: [1, 5, 10, 30, 60, 120, 300, 600]
```

### 4. Retención de Datos

Prometheus almacena datos por 30 días por defecto. Para mayor retención:

```yaml
command:
  - '--storage.tsdb.retention.time=90d'
```

### 5. Performance

- Evitar queries costosas en dashboards de alta frecuencia
- Usar `rate()` en lugar de `irate()` para series más suaves
- Agregar datos con `sum()`, `avg()` antes de graficar

## Troubleshooting

### Prometheus no puede scrapear métricas

**Causa:** Aplicación no accesible desde contenedor

**Solución:**
1. Verificar que la app corre en puerto 3000
2. Usar `host.docker.internal` en lugar de `localhost` en prometheus.yml
3. Verificar que `/metrics` responde: `curl http://localhost:3000/metrics`

### Métricas no aparecen en Grafana

**Causa:** Datasource no configurado o sin datos

**Solución:**
1. Verificar datasource en Grafana → Configuration → Data Sources
2. Probar query en Prometheus: http://localhost:9090
3. Verificar que hay datos: `up{job="sqm-api"}`

### Dashboard vacío

**Causa:** Queries incorrectas o sin datos en rango de tiempo

**Solución:**
1. Verificar rango de tiempo (últimas 1h, 6h, 24h)
2. Probar query simple primero: `up`
3. Revisar sintaxis PromQL

### Alta memoria en Prometheus

**Causa:** Muchas métricas o alta cardinalidad

**Solución:**
1. Revisar número de series: http://localhost:9090/api/v1/status/tsdb
2. Reducir labels con alta cardinalidad
3. Aumentar límites de memoria en docker-compose:
   ```yaml
   deploy:
     resources:
       limits:
         memory: 2G
   ```

## Monitoreo en Producción

### Health Check de Prometheus

```bash
# Verificar que Prometheus está up
curl http://localhost:9090/-/healthy

# Verificar targets
curl http://localhost:9090/api/v1/targets
```

### Backup de Datos

```bash
# Crear snapshot
curl -XPOST http://localhost:9090/api/v1/admin/tsdb/snapshot

# Snapshots están en /prometheus/snapshots/
```

### Escalabilidad

Para mayor volumen de métricas:

1. **Prometheus Federation** - Múltiples Prometheus, uno central
2. **Thanos/Cortex** - Almacenamiento de largo plazo
3. **VictoriaMetrics** - Alternativa más eficiente

## Roadmap

### Próximas Mejoras

1. **Reglas de Alertas**
   - Configurar Alertmanager
   - Integrar con Slack/Email

2. **Dashboards Adicionales**
   - Dashboard por servicio externo
   - Dashboard de negocio con KPIs

3. **Tracing Distribuido**
   - Integrar OpenTelemetry
   - Jaeger para tracing

4. **Logs Centralizados**
   - ELK Stack (Elasticsearch, Logstash, Kibana)
   - Loki como alternativa ligera

---

**Implementado en**: Fase 2 - Semana 1
**Fecha**: 2025-11-17
**Versión**: 1.0
