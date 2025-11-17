# Configuración y Uso de Autenticación JWT

**Fecha:** 2025-11-17
**Versión:** 1.0

---

## 📋 Resumen

Se ha implementado un sistema de autenticación JWT (JSON Web Tokens) para proteger los endpoints de la API. Este documento explica cómo configurarlo y usarlo.

---

## 🚀 Configuración Inicial

### Paso 1: Generar JWT Secret

Genera una clave secreta aleatoria para firmar los tokens:

```bash
node -e "console.log(require('crypto').randomBytes(64).toString('hex'))"
```

Copia el resultado y agrégalo a tu archivo `.env`:

```env
JWT_SECRET=tu_clave_secreta_generada_aqui
```

### Paso 2: Generar Hash de Contraseña

**EN DESARROLLO:**

1. Inicia el servidor
2. Genera el hash de tu contraseña deseada:

```bash
curl -X POST http://localhost:3001/auth/generate-hash \
  -H "Content-Type: application/json" \
  -d '{"password": "tu_contraseña_segura"}'
```

Respuesta:
```json
{
  "password": "tu_contraseña_segura",
  "hash": "$2a$10$...",
  "instructions": "Copie el hash y agréguelo como API_PASSWORD_HASH en su archivo .env"
}
```

3. Copia el `hash` y agrégalo a tu `.env`:

```env
API_PASSWORD_HASH=$2a$10$...hash_completo...
```

**IMPORTANTE:** En producción, elimina el endpoint `/auth/generate-hash` o protégelo.

### Paso 3: Configurar Variables de Entorno

Tu archivo `.env` debe tener:

```env
# Autenticación JWT
JWT_SECRET=clave_secreta_aleatoria_de_64_caracteres_en_hex
JWT_EXPIRES_IN=24h
API_USERNAME=admin
API_PASSWORD_HASH=$2a$10$...hash_de_tu_contraseña...
NODE_ENV=development
```

### Paso 4: Reiniciar el Servidor

```bash
npm run dev
```

---

## 🔐 Uso de la API

### 1. Login (Obtener Token)

**Endpoint:** `POST /auth/login`

**Request:**
```bash
curl -X POST http://localhost:3001/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "tu_contraseña"
  }'
```

**Response Exitoso:**
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expiresIn": "24h",
  "user": {
    "username": "admin",
    "role": "admin"
  }
}
```

**Response Error:**
```json
{
  "error": "Credenciales inválidas",
  "message": "Usuario o contraseña incorrectos"
}
```

### 2. Usar Token en Requests

Una vez obtenido el token, inclúyelo en el header `Authorization` de tus requests:

```bash
curl http://localhost:3001/api/datos-PM10 \
  -H "Authorization: Bearer tu_token_jwt_aqui"
```

### 3. Verificar Token

**Endpoint:** `GET /auth/verify`

```bash
curl http://localhost:3001/auth/verify \
  -H "Authorization: Bearer tu_token_jwt_aqui"
```

**Response:**
```json
{
  "success": true,
  "user": {
    "username": "admin",
    "role": "admin",
    "iat": 1700000000
  },
  "message": "Token válido"
}
```

### 4. Renovar Token

**Endpoint:** `POST /auth/refresh`

```bash
curl -X POST http://localhost:3001/auth/refresh \
  -H "Authorization: Bearer tu_token_actual"
```

**Response:**
```json
{
  "success": true,
  "token": "nuevo_token_jwt_aqui",
  "expiresIn": "24h"
}
```

---

## 🔒 Protección de Endpoints

### Implementación Actual

La infraestructura JWT está lista. Los endpoints de la API (`/api/*`) actualmente NO requieren autenticación para facilitar la migración progresiva.

### Proteger Endpoints (Próximo Paso)

Para proteger endpoints específicos, edita `server/routes/apiRoutes.js`:

```javascript
const { verifyToken, requireRole } = require('../middleware/authMiddleware');

// Endpoint protegido - requiere autenticación
router.get('/datos-PM10',
  verifyToken,  // <-- Agregar este middleware
  graphDataLimiter,
  ctl.getPM10Data
);

// Endpoint protegido con rol específico
router.post('/admin/config',
  verifyToken,
  requireRole('admin'),  // <-- Solo admins
  adminController.updateConfig
);
```

### Estrategia de Migración Progresiva

1. **Fase 1 (Actual):**
   - Sistema JWT implementado
   - Endpoints públicos (sin autenticación)
   - Frontend puede empezar a implementar login

2. **Fase 2 (Próxima):**
   - Proteger endpoints críticos (admin, configuración)
   - Endpoints de lectura siguen públicos

3. **Fase 3 (Final):**
   - Todos los endpoints requieren autenticación
   - Rate limiting por usuario (no por IP)

---

## 🧪 Testing

### Test Manual con curl

**1. Login:**
```bash
TOKEN=$(curl -s -X POST http://localhost:3001/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"tu_password"}' \
  | jq -r '.token')

echo "Token: $TOKEN"
```

**2. Usar token:**
```bash
curl http://localhost:3001/api/datos-PM10 \
  -H "Authorization: Bearer $TOKEN"
```

**3. Token inválido:**
```bash
curl http://localhost:3001/api/datos-PM10 \
  -H "Authorization: Bearer token_invalido"
```

### Test con Postman

1. **Login:**
   - Method: POST
   - URL: `http://localhost:3001/auth/login`
   - Body (JSON):
     ```json
     {
       "username": "admin",
       "password": "tu_password"
     }
     ```
   - Guardar el `token` de la respuesta

2. **Usar Token:**
   - En cualquier request a `/api/*`
   - Tab "Authorization"
   - Type: "Bearer Token"
   - Token: pegar el token obtenido

---

## ⚠️ Errores Comunes

### Error: "SERPRAM_TOKEN no está configurado"

**Causa:** Cambio reciente, el token ahora debe estar en `.env`

**Solución:**
1. Abrir `server/.env`
2. Agregar línea: `SERPRAM_TOKEN=tu_token_serpram`
3. Reiniciar servidor

### Error: "JWT_SECRET no está configurado"

**Causa:** Falta configurar la clave secreta

**Solución:**
```bash
# Generar secret
node -e "console.log(require('crypto').randomBytes(64).toString('hex'))"

# Agregar a .env
JWT_SECRET=clave_generada
```

### Error: "API_PASSWORD_HASH no está configurado"

**Causa:** Falta generar el hash de la contraseña

**Solución:**
1. Usar endpoint `/auth/generate-hash`
2. Copiar hash generado a `.env`

### Error: "Token expirado"

**Causa:** El token tiene tiempo de vida limitado

**Solución:**
- Usar endpoint `/auth/refresh` para obtener nuevo token
- O hacer login nuevamente

---

## 🔐 Seguridad

### Mejores Prácticas

**DO (Hacer):**
- ✅ Usar HTTPS en producción
- ✅ Rotar `JWT_SECRET` periódicamente
- ✅ Usar contraseñas fuertes (mínimo 12 caracteres)
- ✅ Configurar `JWT_EXPIRES_IN` apropiado (24h recomendado)
- ✅ Eliminar endpoint `/auth/generate-hash` en producción
- ✅ Monitorear intentos de login fallidos
- ✅ Implementar rate limiting en `/auth/login`

**DON'T (No hacer):**
- ❌ NO exponer `JWT_SECRET` en logs o código
- ❌ NO usar `JWT_SECRET` débil o predecible
- ❌ NO guardar tokens en localStorage (usar httpOnly cookies en producción)
- ❌ NO compartir tokens entre usuarios
- ❌ NO ignorar tokens expirados

### Almacenamiento de Tokens (Frontend)

**Desarrollo:**
```javascript
// Guardar token
localStorage.setItem('token', response.token);

// Usar token
const token = localStorage.getItem('token');
fetch('/api/datos-PM10', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

**Producción (Recomendado):**
- Usar httpOnly cookies
- Implementar refresh token pattern
- CSRF protection

---

## 📊 Monitoreo

### Logs a Revisar

```bash
# Ver logs de autenticación
grep "login" server.log
grep "Token" server.log

# Ver intentos fallidos
grep "Credenciales inválidas" server.log
grep "401" server.log
```

### Métricas a Monitorear

- Tasa de login exitoso vs fallido
- Tokens expirados por día
- Endpoints más accedidos
- IPs con múltiples intentos fallidos (posible ataque)

---

## 🔄 Roadmap

### Implementado ✅

- [x] Middleware de autenticación JWT
- [x] Controlador de autenticación
- [x] Rutas de auth (login, verify, refresh)
- [x] Validación de tokens
- [x] Sistema de roles básico
- [x] Documentación de uso

### Pendiente 📋

- [ ] Proteger endpoints críticos de API
- [ ] Rate limiting específico para /auth/login
- [ ] Migrar a httpOnly cookies (producción)
- [ ] Implementar refresh tokens
- [ ] Logs de auditoría de accesos
- [ ] 2FA (autenticación de dos factores)
- [ ] Gestión de usuarios en BD (múltiples usuarios)

---

## 📞 Soporte

**Problemas con autenticación:**
1. Verificar que todas las variables de entorno estén configuradas
2. Verificar que el servidor esté corriendo
3. Revisar logs del servidor
4. Probar con curl antes de usar desde frontend

**Contacto:**
- Backend Lead: [EMAIL]
- DevOps: [EMAIL]

---

**Última actualización:** 2025-11-17
**Versión de API:** 1.0
