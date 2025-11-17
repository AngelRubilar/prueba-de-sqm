# Configuración de CORS y Headers de Seguridad

**Fecha:** 2025-11-17
**Prioridad:** ALTA
**Estado:** Completado

---

## 📋 Resumen

Se ha implementado una configuración de CORS restrictiva y headers de seguridad que se adaptan automáticamente según el entorno (desarrollo/producción), mejorando significativamente la postura de seguridad de la API.

---

## ⚠️ Problema Anterior

### CORS Comentado / Demasiado Permisivo

```javascript
// ANTES - CORS completamente deshabilitado
// app.use(cors({ origin: true }));  // ❌ Permite CUALQUIER origen
```

**Riesgos:**
- Cualquier sitio web podía hacer requests a la API
- Sin protección contra CSRF
- Sin validación de orígenes
- Headers de seguridad inconsistentes

---

## ✅ Solución Implementada

### CORS Restrictivo Según Entorno

**Archivo:** `server/app.js`

#### Desarrollo:
- Permite origins configurados en `FRONTEND_URL`
- Permite requests sin origin (Postman, curl)
- Headers de seguridad relajados
- CSP deshabilitado

#### Producción:
- SOLO permite origins explícitamente configurados
- Bloquea requests sin origin válido
- Headers de seguridad estrictos
- CSP habilitado
- HSTS habilitado (con HTTPS)

---

## 🔧 Configuración

### Variables de Entorno

**Desarrollo** (`server/.env`):
```env
NODE_ENV=development
ENABLE_CORS=true
FRONTEND_URL=http://localhost:3000
```

**Producción con Frontend Separado** (`server/.env`):
```env
NODE_ENV=production
ENABLE_CORS=true
FRONTEND_URL=https://sqm.mimasoft.cl
```

**Producción en Monorepo (API + Frontend en mismo servidor)**:
```env
NODE_ENV=production
ENABLE_CORS=false  # Deshabilita CORS, permite todas las requests
```

**Múltiples Orígenes** (separados por coma):
```env
ENABLE_CORS=true
FRONTEND_URL=https://sqm.mimasoft.cl,https://monitor.sqm.cl,https://admin.sqm.cl
```

### ⚙️ Modo Monorepo

Si tu aplicación está en un monorepo donde la API y el frontend se sirven desde el mismo servidor/dominio:

```env
# .env en producción
ENABLE_CORS=false
```

**Ventajas:**
- No hay preflight OPTIONS requests
- Mejor performance
- Sin problemas de CORS
- Simplifica configuración

**Cuándo usar:**
- Frontend y API en mismo dominio
- Proxy inverso (Nginx) sirviendo ambos
- Aplicación embebida

---

## 🛡️ Headers de Seguridad Implementados

### 1. CORS Headers

```javascript
Access-Control-Allow-Origin: http://localhost:3000  // Solo origins permitidos
Access-Control-Allow-Credentials: true
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH
Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With
Access-Control-Max-Age: 86400  // 24h cache preflight
```

**Protege contra:**
- Cross-site request forgery (CSRF)
- Acceso no autorizado desde otros dominios

### 2. X-Content-Type-Options

```
X-Content-Type-Options: nosniff
```

**Protege contra:**
- MIME type sniffing attacks
- Ejecución de scripts maliciosos

### 3. X-Frame-Options

**Desarrollo:**
```
X-Frame-Options: SAMEORIGIN  // Permite iframes del mismo origen
```

**Producción:**
```
X-Frame-Options: DENY  // No permite iframes
```

**Protege contra:**
- Clickjacking
- UI redressing attacks

### 4. X-XSS-Protection

```
X-XSS-Protection: 1; mode=block
```

**Protege contra:**
- Cross-site scripting (XSS) en navegadores antiguos

### 5. Referrer-Policy

```
Referrer-Policy: strict-origin-when-cross-origin
```

**Protege contra:**
- Fuga de información en el header Referer
- Privacy leaks

### 6. Content-Security-Policy (Solo Producción)

```
Content-Security-Policy:
  default-src 'self';
  script-src 'self' 'unsafe-inline';
  style-src 'self' 'unsafe-inline';
  img-src 'self' data: https:;
  font-src 'self' data:;
  connect-src 'self';
  frame-ancestors 'none'
```

**Protege contra:**
- XSS attacks
- Injection de código malicioso
- Clickjacking (complementa X-Frame-Options)

### 7. Strict-Transport-Security (Solo Producción con HTTPS)

```
Strict-Transport-Security: max-age=31536000; includeSubDomains
```

**Protege contra:**
- Man-in-the-middle attacks
- Protocol downgrade attacks
- Cookie hijacking

---

## 🧪 Testing

### Test 1: Origen Permitido

```bash
curl -H "Origin: http://localhost:3000" \
     -H "Access-Control-Request-Method: GET" \
     -X OPTIONS \
     http://localhost:3001/api/datos-PM10
```

**Resultado Esperado:**
```
HTTP/1.1 204 No Content
Access-Control-Allow-Origin: http://localhost:3000
Access-Control-Allow-Credentials: true
```

### Test 2: Origen NO Permitido

```bash
curl -H "Origin: http://malicious-site.com" \
     -H "Access-Control-Request-Method: GET" \
     -X OPTIONS \
     http://localhost:3001/api/datos-PM10
```

**Resultado Esperado:**
```
HTTP/1.1 500 Internal Server Error
Error: Origin no permitido por CORS
```

**Log del servidor:**
```
⚠️  CORS bloqueó origin no permitido: http://malicious-site.com
```

### Test 3: Request Sin Origin (Desarrollo)

```bash
curl http://localhost:3001/api/datos-PM10
```

**Resultado en Desarrollo:** ✅ Permitido
**Resultado en Producción:** ❌ Bloqueado

### Test 4: Verificar Headers de Seguridad

```bash
curl -I http://localhost:3001/api/datos-PM10
```

**Headers Esperados:**
```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN  (dev) o DENY (prod)
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Content-Security-Policy: ...  (solo prod)
```

---

## 🔒 Mejores Prácticas

### DO (Hacer):

✅ Configurar `FRONTEND_URL` con URLs completas y específicas
```env
FRONTEND_URL=https://sqm.mimasoft.cl  # Específico
```

✅ Usar HTTPS en producción
```env
FRONTEND_URL=https://sqm.mimasoft.cl  # No http://
```

✅ Mantener `NODE_ENV=production` en producción

✅ Revisar logs de CORS bloqueados regularmente
```bash
grep "CORS bloqueó" logs/combined.log
```

✅ Probar CORS antes de desplegar
```bash
npm run test:cors
```

### DON'T (No Hacer):

❌ NO usar wildcards en producción
```env
FRONTEND_URL=*  # ❌ MUY INSEGURO
```

❌ NO deshabilitar CORS
```javascript
app.use(cors({ origin: true }));  # ❌ Permite cualquier origen
```

❌ NO incluir protocolos mezclados
```env
FRONTEND_URL=http://sqm.com,https://sqm.com  # ❌ Inconsistente
```

❌ NO olvidar actualizar en producción
```env
# ❌ Dejar localhost en producción
FRONTEND_URL=http://localhost:3000
```

---

## 🌍 Configuración por Entorno

### Desarrollo Local

```env
NODE_ENV=development
FRONTEND_URL=http://localhost:3000
```

**Comportamiento:**
- CORS permisivo para development
- Permite requests sin origin
- CSP deshabilitado
- Headers relajados

### Staging

```env
NODE_ENV=production
FRONTEND_URL=https://staging.sqm.mimasoft.cl
```

**Comportamiento:**
- CORS estricto
- Solo origin de staging permitido
- Headers de seguridad habilitados
- CSP habilitado

### Producción

```env
NODE_ENV=production
FRONTEND_URL=https://sqm.mimasoft.cl,https://monitor.sqm.cl
```

**Comportamiento:**
- CORS estricto
- Solo origins de producción permitidos
- Todos los headers de seguridad
- HSTS habilitado (con HTTPS)

---

## 📊 Monitoreo

### Logs a Revisar

```bash
# Ver intentos bloqueados por CORS
grep "CORS bloqueó" logs/combined.log

# Ver origins accediendo
grep "Origin:" logs/access.log

# Ver errores CORS
grep "Origin no permitido" logs/error.log
```

### Métricas Recomendadas

- **CORS blocks/día:** < 10 (normal), > 100 (posible ataque)
- **Origins únicos/día:** Debe ser bajo (1-5)
- **Preflight requests:** Debe ser cacheable (max-age=86400)

---

## 🔧 Troubleshooting

### Error: "Origin no permitido por CORS"

**Causa:** El frontend está en un origin no configurado

**Solución:**
1. Verificar `FRONTEND_URL` en `.env`
2. Verificar que incluye protocolo y puerto
   ```env
   # ✅ Correcto
   FRONTEND_URL=http://localhost:3000

   # ❌ Incorrecto
   FRONTEND_URL=localhost:3000
   ```
3. Reiniciar servidor después de cambiar `.env`

### Error: Preflight OPTIONS falla

**Causa:** Middleware de autenticación bloqueando OPTIONS

**Solución:** Verificar que CORS está ANTES de middlewares de auth:
```javascript
app.use(cors(corsOptions));  // ✅ Primero
app.use('/api', authMiddleware);  // Después
```

### Warning: CSRF en desarrollo

**Causa:** Requests sin origin permitidos en desarrollo

**Solución:** Normal en desarrollo, se bloquea en producción

---

## 🚀 Despliegue

### Checklist Pre-Producción

- [ ] `NODE_ENV=production` configurado
- [ ] `FRONTEND_URL` con URL de producción (HTTPS)
- [ ] No incluye `localhost` ni IPs privadas
- [ ] HTTPS habilitado en servidor
- [ ] Headers de seguridad verificados
- [ ] CORS testeado con frontend de producción

### Verificación Post-Despliegue

```bash
# Verificar CORS
curl -H "Origin: https://sqm.mimasoft.cl" \
     -I https://api.sqm.mimasoft.cl/api/health

# Verificar headers de seguridad
curl -I https://api.sqm.mimasoft.cl/api/health | grep -E "(X-|Content-Security|Strict-Transport)"

# Verificar CSP
curl -I https://api.sqm.mimasoft.cl/api/health | grep Content-Security-Policy
```

---

## 📈 Roadmap

### Completado ✅

- [x] CORS restrictivo según entorno
- [x] Headers de seguridad completos
- [x] Validación de origins
- [x] CSP básico en producción
- [x] HSTS en producción con HTTPS
- [x] Logging de blocks

### Próximos Pasos 📋

- [ ] CSP más estricto (eliminar 'unsafe-inline')
- [ ] Implementar nonce para scripts inline
- [ ] Reportes de violaciones CSP
- [ ] CSRF tokens explícitos
- [ ] Subresource Integrity (SRI)
- [ ] Permissions Policy
- [ ] Certificate pinning (HPKP)

---

## 📞 Soporte

**Problemas con CORS:**
1. Verificar `FRONTEND_URL` en `.env`
2. Verificar `NODE_ENV`
3. Revisar logs del servidor
4. Probar con curl antes de frontend

**Contacto:**
- Backend Lead: [EMAIL]
- Security: [EMAIL]

---

**Última actualización:** 2025-11-17
**Versión:** 1.0
