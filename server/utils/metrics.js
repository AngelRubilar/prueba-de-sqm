const client = require('prom-client');
const logger = require('../config/logger');

/**
 * Módulo de Métricas Prometheus
 *
 * Colecta y expone métricas de la aplicación en formato Prometheus
 */

// Crear un registro de métricas
const register = new client.Registry();

// Habilitar colección de métricas por defecto (CPU, memoria, etc.)
client.collectDefaultMetrics({
  register,
  prefix: 'sqm_',
  gcDurationBuckets: [0.001, 0.01, 0.1, 1, 2, 5]
});

// ==================== MÉTRICAS HTTP ====================

/**
 * Contador de requests HTTP por método, ruta y código de estado
 */
const httpRequestsTotal = new client.Counter({
  name: 'sqm_http_requests_total',
  help: 'Total de requests HTTP recibidas',
  labelNames: ['method', 'route', 'status_code'],
  registers: [register]
});

/**
 * Histograma de duración de requests HTTP
 */
const httpRequestDuration = new client.Histogram({
  name: 'sqm_http_request_duration_seconds',
  help: 'Duración de requests HTTP en segundos',
  labelNames: ['method', 'route', 'status_code'],
  buckets: [0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1, 2.5, 5, 10],
  registers: [register]
});

/**
 * Gauge de requests HTTP activas
 */
const httpRequestsInProgress = new client.Gauge({
  name: 'sqm_http_requests_in_progress',
  help: 'Número de requests HTTP en progreso',
  labelNames: ['method', 'route'],
  registers: [register]
});

// ==================== MÉTRICAS DE APIs EXTERNAS ====================

/**
 * Contador de llamadas a APIs externas
 */
const externalApiCallsTotal = new client.Counter({
  name: 'sqm_external_api_calls_total',
  help: 'Total de llamadas a APIs externas',
  labelNames: ['api', 'status'],
  registers: [register]
});

/**
 * Histograma de duración de llamadas a APIs externas
 */
const externalApiDuration = new client.Histogram({
  name: 'sqm_external_api_duration_seconds',
  help: 'Duración de llamadas a APIs externas en segundos',
  labelNames: ['api'],
  buckets: [0.1, 0.5, 1, 2, 5, 10, 20, 30],
  registers: [register]
});

/**
 * Gauge de errores consecutivos en APIs externas
 */
const externalApiConsecutiveErrors = new client.Gauge({
  name: 'sqm_external_api_consecutive_errors',
  help: 'Número de errores consecutivos en APIs externas',
  labelNames: ['api'],
  registers: [register]
});

// ==================== MÉTRICAS DE CIRCUIT BREAKERS ====================

/**
 * Gauge del estado de Circuit Breakers
 * 0 = CLOSED, 1 = HALF_OPEN, 2 = OPEN
 */
const circuitBreakerState = new client.Gauge({
  name: 'sqm_circuit_breaker_state',
  help: 'Estado del Circuit Breaker (0=CLOSED, 1=HALF_OPEN, 2=OPEN)',
  labelNames: ['circuit'],
  registers: [register]
});

/**
 * Contador de eventos de Circuit Breaker
 */
const circuitBreakerEvents = new client.Counter({
  name: 'sqm_circuit_breaker_events_total',
  help: 'Total de eventos de Circuit Breaker',
  labelNames: ['circuit', 'event'],
  registers: [register]
});

/**
 * Gauge de tasa de éxito de Circuit Breakers
 */
const circuitBreakerSuccessRate = new client.Gauge({
  name: 'sqm_circuit_breaker_success_rate',
  help: 'Tasa de éxito del Circuit Breaker (0-100)',
  labelNames: ['circuit'],
  registers: [register]
});

// ==================== MÉTRICAS DE COLAS (BULL QUEUE) ====================

/**
 * Gauge de jobs en diferentes estados
 */
const queueJobsGauge = new client.Gauge({
  name: 'sqm_queue_jobs',
  help: 'Número de jobs en cada estado',
  labelNames: ['queue', 'state'],
  registers: [register]
});

/**
 * Contador de jobs procesados
 */
const queueJobsProcessed = new client.Counter({
  name: 'sqm_queue_jobs_processed_total',
  help: 'Total de jobs procesados',
  labelNames: ['queue', 'status'],
  registers: [register]
});

/**
 * Histograma de duración de procesamiento de jobs
 */
const queueJobDuration = new client.Histogram({
  name: 'sqm_queue_job_duration_seconds',
  help: 'Duración de procesamiento de jobs en segundos',
  labelNames: ['queue'],
  buckets: [1, 5, 10, 30, 60, 120, 300, 600],
  registers: [register]
});

// ==================== MÉTRICAS DE BASE DE DATOS ====================

/**
 * Contador de queries a la base de datos
 */
const dbQueriesTotal = new client.Counter({
  name: 'sqm_db_queries_total',
  help: 'Total de queries ejecutadas',
  labelNames: ['pool', 'status'],
  registers: [register]
});

/**
 * Histograma de duración de queries
 */
const dbQueryDuration = new client.Histogram({
  name: 'sqm_db_query_duration_seconds',
  help: 'Duración de queries en segundos',
  labelNames: ['pool'],
  buckets: [0.001, 0.005, 0.01, 0.05, 0.1, 0.5, 1, 2, 5],
  registers: [register]
});

/**
 * Gauge de conexiones de base de datos
 */
const dbConnections = new client.Gauge({
  name: 'sqm_db_connections',
  help: 'Número de conexiones de base de datos',
  labelNames: ['pool', 'state'],
  registers: [register]
});

// ==================== MÉTRICAS DE REDIS ====================

/**
 * Contador de operaciones Redis
 */
const redisOperationsTotal = new client.Counter({
  name: 'sqm_redis_operations_total',
  help: 'Total de operaciones Redis',
  labelNames: ['operation', 'status'],
  registers: [register]
});

/**
 * Histograma de duración de operaciones Redis
 */
const redisOperationDuration = new client.Histogram({
  name: 'sqm_redis_operation_duration_seconds',
  help: 'Duración de operaciones Redis en segundos',
  labelNames: ['operation'],
  buckets: [0.001, 0.005, 0.01, 0.05, 0.1, 0.5, 1],
  registers: [register]
});

// ==================== MÉTRICAS DE NEGOCIO ====================

/**
 * Contador de registros insertados por estación
 */
const dataRecordsInserted = new client.Counter({
  name: 'sqm_data_records_inserted_total',
  help: 'Total de registros de datos insertados',
  labelNames: ['station', 'source'],
  registers: [register]
});

/**
 * Gauge de última sincronización exitosa
 */
const lastSuccessfulSync = new client.Gauge({
  name: 'sqm_last_successful_sync_timestamp',
  help: 'Timestamp de la última sincronización exitosa',
  labelNames: ['source'],
  registers: [register]
});

// ==================== FUNCIONES AUXILIARES ====================

/**
 * Middleware para colectar métricas HTTP
 */
function metricsMiddleware() {
  return (req, res, next) => {
    const route = req.route ? req.route.path : req.path;
    const method = req.method;

    // Incrementar requests en progreso
    httpRequestsInProgress.labels(method, route).inc();

    // Iniciar timer
    const end = httpRequestDuration.startTimer({ method, route });

    // Interceptar el final de la response
    res.on('finish', () => {
      const statusCode = res.statusCode;

      // Decrementar requests en progreso
      httpRequestsInProgress.labels(method, route).dec();

      // Registrar duración
      end({ status_code: statusCode });

      // Incrementar contador total
      httpRequestsTotal.labels(method, route, statusCode).inc();
    });

    next();
  };
}

/**
 * Actualizar métricas de Circuit Breakers
 */
function updateCircuitBreakerMetrics(circuitName, stats) {
  // Estado del circuito
  const stateValue = stats.state === 'CLOSED' ? 0 :
                    stats.state === 'HALF_OPEN' ? 1 : 2;
  circuitBreakerState.labels(circuitName).set(stateValue);

  // Tasa de éxito
  const successRate = stats.fires > 0
    ? (stats.successes / stats.fires) * 100
    : 100;
  circuitBreakerSuccessRate.labels(circuitName).set(successRate);
}

/**
 * Actualizar métricas de colas
 */
function updateQueueMetrics(queueName, stats) {
  queueJobsGauge.labels(queueName, 'waiting').set(stats.waiting);
  queueJobsGauge.labels(queueName, 'active').set(stats.active);
  queueJobsGauge.labels(queueName, 'completed').set(stats.completed);
  queueJobsGauge.labels(queueName, 'failed').set(stats.failed);
  queueJobsGauge.labels(queueName, 'delayed').set(stats.delayed);
}

/**
 * Actualizar métricas de conexiones de base de datos
 */
function updateDatabaseMetrics(poolName, connections) {
  dbConnections.labels(poolName, 'total').set(connections.total || 0);
  dbConnections.labels(poolName, 'free').set(connections.free || 0);
  dbConnections.labels(poolName, 'queued').set(connections.queued || 0);
}

/**
 * Registrar llamada a API externa
 */
function recordExternalApiCall(apiName, success, duration) {
  const status = success ? 'success' : 'error';

  externalApiCallsTotal.labels(apiName, status).inc();

  if (duration) {
    externalApiDuration.labels(apiName).observe(duration / 1000);
  }
}

/**
 * Registrar operación Redis
 */
function recordRedisOperation(operation, success, duration) {
  const status = success ? 'success' : 'error';

  redisOperationsTotal.labels(operation, status).inc();

  if (duration) {
    redisOperationDuration.labels(operation).observe(duration / 1000);
  }
}

/**
 * Registrar query de base de datos
 */
function recordDatabaseQuery(poolName, success, duration) {
  const status = success ? 'success' : 'error';

  dbQueriesTotal.labels(poolName, status).inc();

  if (duration) {
    dbQueryDuration.labels(poolName).observe(duration / 1000);
  }
}

/**
 * Registrar inserción de datos
 */
function recordDataInsertion(station, source, count = 1) {
  dataRecordsInserted.labels(station, source).inc(count);
}

/**
 * Actualizar timestamp de última sincronización
 */
function updateLastSync(source) {
  lastSuccessfulSync.labels(source).set(Date.now() / 1000);
}

/**
 * Obtener todas las métricas en formato Prometheus
 */
async function getMetrics() {
  return await register.metrics();
}

/**
 * Obtener métricas en formato JSON
 */
async function getMetricsJSON() {
  return await register.getMetricsAsJSON();
}

/**
 * Resetear todas las métricas
 */
function resetMetrics() {
  register.resetMetrics();
  logger.info('Métricas reseteadas');
}

// Exportar registro y métricas
module.exports = {
  register,

  // Métricas
  httpRequestsTotal,
  httpRequestDuration,
  httpRequestsInProgress,
  externalApiCallsTotal,
  externalApiDuration,
  circuitBreakerState,
  circuitBreakerEvents,
  queueJobsGauge,
  queueJobsProcessed,
  queueJobDuration,
  dbQueriesTotal,
  dbQueryDuration,
  dbConnections,
  redisOperationsTotal,
  redisOperationDuration,
  dataRecordsInserted,
  lastSuccessfulSync,

  // Funciones
  metricsMiddleware,
  updateCircuitBreakerMetrics,
  updateQueueMetrics,
  updateDatabaseMetrics,
  recordExternalApiCall,
  recordRedisOperation,
  recordDatabaseQuery,
  recordDataInsertion,
  updateLastSync,
  getMetrics,
  getMetricsJSON,
  resetMetrics
};
