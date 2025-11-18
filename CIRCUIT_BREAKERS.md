# Circuit Breakers - Sistema de Resiliencia para APIs Externas

## Descripción General

Se han implementado Circuit Breakers en todas las APIs externas del sistema para mejorar la resiliencia y evitar que fallos en servicios externos afecten la disponibilidad general de la aplicación.

## ¿Qué es un Circuit Breaker?

Un Circuit Breaker es un patrón de diseño que protege a la aplicación de fallos en cascada cuando un servicio externo no está disponible o responde lentamente. Funciona de manera similar a un interruptor eléctrico:

### Estados del Circuit Breaker

1. **CLOSED (Cerrado)**: Estado normal - Las peticiones fluyen normalmente
2. **OPEN (Abierto)**: Demasiados errores detectados - Las peticiones se rechazan inmediatamente sin llamar a la API
3. **HALF_OPEN (Semi-abierto)**: Período de prueba - Se permite un número limitado de peticiones para verificar si el servicio se recuperó

## Implementación

### Librería Utilizada

- **opossum**: Circuit Breaker para Node.js con soporte completo para promesas y async/await

### Configuración por API

#### SERPRAM
```javascript
{
  timeout: 20000,                    // 20 segundos
  errorThresholdPercentage: 50,      // Se abre si 50% de requests fallan
  resetTimeout: 60000                // Intenta cerrar después de 1 minuto
}
```

#### AYT
```javascript
{
  timeout: 15000,                    // 15 segundos
  errorThresholdPercentage: 50,
  resetTimeout: 45000                // 45 segundos (consulta cada 1 min)
}
```

#### ESINFA
```javascript
{
  timeout: 15000,                    // 15 segundos
  errorThresholdPercentage: 50,
  resetTimeout: 60000                // 1 minuto
}
```

#### SERCOAMB
- **Tamentica**:
  ```javascript
  {
    timeout: 15000,
    errorThresholdPercentage: 50,
    resetTimeout: 60000
  }
  ```

- **Victoria**:
  ```javascript
  {
    timeout: 15000,
    errorThresholdPercentage: 50,
    resetTimeout: 60000
  }
  ```

## Funcionalidades Implementadas

### 1. Protección de Llamadas a APIs

Cada servicio ahora tiene un método privado `_makeApiRequest` que realiza la llamada real a la API, y está protegido por el Circuit Breaker.

**Ejemplo en SerpramService:**
```javascript
async _makeApiRequest(dispositivo, estampaTiempoInicial, estampaTiempoFinal) {
  const token = await authService.getToken();
  const config = { /* configuración de axios */ };
  const response = await axios.request(config);
  return response.data;
}

async consultarAPI(dispositivo, timestampDesde = null) {
  try {
    const { estampaTiempoInicial, estampaTiempoFinal } = this.obtenerMarcasDeTiempo(timestampDesde);

    // Circuit Breaker protege la llamada
    const data = await this.circuitBreaker.fire(dispositivo, estampaTiempoInicial, estampaTiempoFinal);

    if (data?.fallback) {
      logger.warn(`SERPRAM API no disponible, usando fallback`);
      return [];
    }

    return this.transformarRespuesta(data, dispositivo);
  } catch (error) {
    if (error.message && error.message.includes('Breaker is open')) {
      logger.warn(`Circuit Breaker abierto, retornando datos vacíos`);
      return [];
    }
    throw error;
  }
}
```

### 2. Fallback Automático

Cuando el Circuit Breaker está abierto, se retorna un fallback seguro (array vacío) en lugar de fallar completamente.

### 3. Logging de Eventos

Todos los eventos del Circuit Breaker se registran en logs:

- `open`: Circuit abierto por demasiados errores
- `halfOpen`: Probando si el servicio se recuperó
- `close`: Servicio funcionando normalmente
- `timeout`: Request excedió el tiempo límite
- `reject`: Request rechazada (circuito abierto)
- `success`: Request exitosa
- `failure`: Request falló

### 4. Estadísticas en Tiempo Real

Cada servicio expone un método `getCircuitBreakerStats()` para obtener métricas:

```javascript
const stats = serpramService.getCircuitBreakerStats();
console.log(stats);
// Output:
// {
//   name: 'SERPRAM',
//   state: 'CLOSED',
//   fires: 150,
//   successes: 145,
//   failures: 5,
//   rejects: 0,
//   timeouts: 2
// }
```

## Beneficios

### 1. Resiliencia Mejorada
- El sistema continúa funcionando aunque una API externa falle
- Los errores se detectan rápidamente y se evita sobrecargar servicios caídos

### 2. Mejor Experiencia de Usuario
- Tiempos de respuesta más predecibles
- No se bloquea esperando a servicios que no responden

### 3. Recuperación Automática
- El Circuit Breaker intenta automáticamente reconectar cuando un servicio se recupera
- No requiere intervención manual

### 4. Observabilidad
- Logs detallados de todos los eventos
- Estadísticas en tiempo real del estado de cada API

## Configuración y Ajustes

### Variables de Entorno

No se requieren variables adicionales. Los Circuit Breakers usan la configuración existente de las APIs.

### Ajustar Parámetros

Si necesitas ajustar la configuración de un Circuit Breaker, modifica los parámetros en el constructor del servicio:

```javascript
this.circuitBreaker = createApiCircuitBreaker(
  'API_NAME',
  this._makeApiRequest.bind(this),
  {
    timeout: 15000,                   // Tiempo máximo de espera
    errorThresholdPercentage: 50,     // % de errores para abrir circuito
    resetTimeout: 60000               // Tiempo antes de intentar cerrar
  }
);
```

### Recomendaciones por Tipo de API

**APIs Lentas:**
- Aumentar `timeout` (ej. 30000ms)
- Aumentar `resetTimeout` (ej. 120000ms)

**APIs Críticas:**
- Reducir `errorThresholdPercentage` (ej. 30%)
- Reducir `resetTimeout` para intentar recuperar más rápido

**APIs de Alta Frecuencia:**
- Reducir `resetTimeout` (ej. 30000ms)
- Considerar implementar rate limiting adicional

## Monitoreo

### Health Check Endpoint

Se puede crear un endpoint para verificar el estado de todos los Circuit Breakers:

```javascript
router.get('/health/circuit-breakers', (req, res) => {
  const stats = {
    serpram: serpramService.getCircuitBreakerStats(),
    ayt: aytService.getCircuitBreakerStats(),
    esinfa: esinfaService.getCircuitBreakerStats(),
    sercoamb: sercoambService.getCircuitBreakerStats()
  };

  const allHealthy = Object.values(stats).every(s =>
    s.state === 'CLOSED' || s.state === 'HALF_OPEN'
  );

  res.status(allHealthy ? 200 : 503).json({
    status: allHealthy ? 'healthy' : 'degraded',
    circuitBreakers: stats
  });
});
```

### Métricas Importantes

1. **fires**: Total de peticiones procesadas
2. **successes**: Peticiones exitosas
3. **failures**: Peticiones fallidas
4. **rejects**: Peticiones rechazadas (circuito abierto)
5. **timeouts**: Peticiones que excedieron el timeout

**Calcular tasa de éxito:**
```javascript
const successRate = (successes / fires) * 100;
```

## Troubleshooting

### Circuit Breaker permanece OPEN

**Causa**: El servicio externo no se ha recuperado

**Solución**:
1. Verificar que el servicio externo esté disponible
2. Revisar logs para identificar el error específico
3. Aumentar `resetTimeout` si el servicio tarda en recuperarse

### Demasiados Timeouts

**Causa**: El servicio externo es lento o timeout muy bajo

**Solución**:
1. Aumentar el valor de `timeout`
2. Optimizar la consulta a la API si es posible
3. Considerar implementar cache

### Circuit se abre frecuentemente

**Causa**: Servicio inestable o `errorThresholdPercentage` muy bajo

**Solución**:
1. Investigar la causa de los errores en el servicio externo
2. Aumentar `errorThresholdPercentage` si los errores son esperados
3. Implementar retry logic antes del Circuit Breaker

## Próximos Pasos

### Fase 2 - Mejoras Planificadas

1. **Dashboard de Monitoreo**
   - Visualización en tiempo real del estado de Circuit Breakers
   - Gráficos de tendencias de errores

2. **Alertas Automáticas**
   - Notificar cuando un Circuit se abre
   - Alertas por alta tasa de errores

3. **Métricas Avanzadas**
   - Integración con Prometheus
   - Histogramas de latencia
   - Percentiles (p50, p90, p95, p99)

4. **Circuit Breaker Distribuido**
   - Estado compartido entre instancias usando Redis
   - Coordinación en ambientes con múltiples servidores

## Referencias

- [Opossum Documentation](https://nodeshift.dev/opossum/)
- [Circuit Breaker Pattern](https://martinfowler.com/bliki/CircuitBreaker.html)
- [Release It! (libro)](https://pragprog.com/titles/mnee2/release-it-second-edition/)

---

**Implementado en**: Fase 2 - Semana 1
**Fecha**: 2025-11-17
**Versión**: 1.0
