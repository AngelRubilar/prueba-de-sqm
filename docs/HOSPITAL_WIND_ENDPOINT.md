# Endpoint de Datos de Viento - Estación Hospital

## Descripción

Este endpoint proporciona datos de velocidad del viento (VV) y dirección del viento (DV) específicos de la estación Hospital (E5) para los últimos 2 días.

## Endpoint

```
GET /api/datos-viento-hospital
```

## Características

### Implementación de Caché Redis
- **Clave de caché**: `measurements:hospital:wind`
- **TTL**: 24 horas
- **Actualización**: Cada 1 minuto si hay nuevos datos
- **Rango de datos**: Últimos 2 días desde el momento de la solicitud

### Rate Limiting
- Utiliza el mismo rate limiter que otros endpoints de gráficos (`graphDataLimiter`)
- Protege contra abuso y sobrecarga del servidor

## Formato de Respuesta

### Estructura JSON
```json
[
  {
    "timestamp": "2024-01-15T10:30:00.000Z",
    "station_name": "E5",
    "velocidad": 5.2,
    "direccion": 180.5
  },
  {
    "timestamp": "2024-01-15T10:20:00.000Z",
    "station_name": "E5",
    "velocidad": 4.8,
    "direccion": 175.2
  }
]
```

### Campos
- **timestamp**: Marca de tiempo ISO 8601
- **station_name**: Código de la estación (siempre "E5" para Hospital)
- **velocidad**: Velocidad del viento en m/s (puede ser null si no hay datos)
- **direccion**: Dirección del viento en grados (puede ser null si no hay datos)

## Implementación Técnica

### Base de Datos
- **Tabla**: `datos`
- **Filtros**: 
  - `station_name = 'E5'`
  - `variable_name IN ('VV', 'DV')`
  - `timestamp >= DATE_SUB(NOW(), INTERVAL 2 DAY)`

### Caché Redis
- **Estrategia**: Sorted Set con timestamp como score
- **Limpieza automática**: Elimina datos más antiguos que 2 días
- **Fallback**: Si Redis falla, obtiene datos directamente de la BD

### Logging
- Diagnóstico detallado de operaciones de caché
- Información de rendimiento y conteo de registros
- Manejo de errores con fallback a BD

## Uso en el Frontend

### Importación
```javascript
import { fetchHospitalWindData } from '../services/api';
```

### Ejemplo de Uso
```javascript
const windData = await fetchHospitalWindData();
// windData contiene array de objetos con velocidad y dirección
```

## Ventajas

1. **Rendimiento optimizado**: Caché Redis reduce carga en BD
2. **Datos específicos**: Solo datos de la estación Hospital
3. **Formato consistente**: Misma estructura que otros endpoints
4. **Actualización automática**: Datos siempre actualizados
5. **Manejo de errores**: Fallback robusto a BD

## Monitoreo

El endpoint incluye logging detallado para:
- Diagnóstico de caché Redis
- Conteo de registros almacenados
- Timestamps de primer y último registro
- Errores y fallbacks

## Compatibilidad

- Compatible con el componente `HospitalDashboard.js`
- Utiliza los mismos rate limiters que endpoints existentes
- Formato consistente con otros endpoints de datos 