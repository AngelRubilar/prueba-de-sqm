# Instrucciones para Rotación del Token SERPRAM

**Fecha de creación:** 2025-11-17
**Prioridad:** CRÍTICA
**Responsable:** Administrador del sistema

---

## ⚠️ ACCIÓN INMEDIATA REQUERIDA

El token de SERPRAM actualmente hardcodeado en el código ha sido removido y movido a variables de entorno. **DEBE ser rotado inmediatamente** por razones de seguridad.

---

## 📋 Pasos para Rotar el Token

### 1. Obtener Nuevo Token de SERPRAM

Contactar al administrador de SERPRAM para solicitar un nuevo token JWT.

**Información de contacto:**
- URL API: https://api.serpram.cl/air_ws/v1/api
- Método de autenticación: JWT Bearer Token

### 2. Actualizar Variables de Entorno

Una vez obtenido el nuevo token, actualizarlo en los archivos de configuración:

#### En Desarrollo Local:

Editar el archivo `server/.env`:

```bash
# Reemplazar el valor existente
SERPRAM_TOKEN=nuevo_token_jwt_aqui
```

#### En Producción (Docker):

**Opción A - Variables de Entorno (Temporal):**
```bash
# En docker-compose.yml o al ejecutar el contenedor
-e SERPRAM_TOKEN=nuevo_token_jwt_aqui
```

**Opción B - Docker Secrets (Recomendado):**
```bash
# Crear el secret
echo "nuevo_token_jwt_aqui" | docker secret create serpram_token -

# Actualizar docker-compose.yml para usar el secret
secrets:
  serpram_token:
    external: true

services:
  app:
    secrets:
      - serpram_token
    environment:
      - SERPRAM_TOKEN_FILE=/run/secrets/serpram_token
```

### 3. Verificar el Token Viejo NO esté en Git

```bash
# Buscar en el historial de git
git log --all --full-history -p -S "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9"

# Si aparece, ver SEC-002 para limpiar el historial
```

### 4. Reiniciar la Aplicación

```bash
# En desarrollo
npm run dev

# En producción (Docker)
docker-compose restart app
```

### 5. Verificar que Funciona

```bash
# Revisar los logs del servidor
docker logs -f nombre_contenedor_app

# Debe aparecer:
# "✅ Consultando Serpram desde: ..."
# Sin errores de autenticación 401
```

---

## 🔒 Mejores Prácticas de Seguridad

### Token Rotation Schedule

- **Frecuencia recomendada:** Cada 90 días
- **Próxima rotación:** [FECHA + 90 días desde hoy]

### Checklist de Seguridad

- [ ] Nuevo token generado
- [ ] Token actualizado en `.env` (desarrollo)
- [ ] Token actualizado en producción (Docker secrets)
- [ ] Token viejo revocado en SERPRAM
- [ ] Verificar que token viejo NO está en git history
- [ ] Aplicación reiniciada y funcionando
- [ ] Documentar fecha de rotación en este archivo

### Almacenamiento Seguro

**NUNCA:**
- ❌ Commitear el token en git
- ❌ Compartir el token por email/slack sin encriptar
- ❌ Hardcodear el token en el código
- ❌ Exponer el token en logs

**SIEMPRE:**
- ✅ Usar variables de entorno
- ✅ Usar Docker secrets en producción
- ✅ Agregar `.env` a `.gitignore`
- ✅ Compartir tokens usando gestores de secretos (Vault, AWS Secrets Manager)

---

## 📝 Historial de Rotaciones

| Fecha | Rotado Por | Razón | Notas |
|-------|------------|-------|-------|
| 2025-11-17 | [NOMBRE] | Migración a .env + Security fix | Token removido de código fuente |
| | | | |
| | | | |

---

## 🆘 Troubleshooting

### Error: "SERPRAM_TOKEN no está configurado"

**Causa:** La variable de entorno no está definida.

**Solución:**
1. Verificar que existe el archivo `server/.env`
2. Verificar que contiene la línea `SERPRAM_TOKEN=...`
3. Reiniciar la aplicación

### Error 401: Unauthorized

**Causa:** Token inválido o expirado.

**Solución:**
1. Contactar administrador de SERPRAM para nuevo token
2. Actualizar `.env` con nuevo token
3. Reiniciar aplicación

### No se obtienen datos de SERPRAM

**Causa:** Puede ser problema de token o de conectividad.

**Solución:**
```bash
# Probar manualmente la API
curl -X GET 'https://api.serpram.cl/air_ws/v1/api/getHistorico' \
  -H 'Authorization: Bearer TU_TOKEN_AQUI' \
  -H 'Content-Type: application/json'
```

---

## 📞 Contactos de Soporte

**SERPRAM API:**
- URL: https://api.serpram.cl
- Documentación: [URL si está disponible]
- Soporte: [email/teléfono]

**Equipo Interno:**
- DevOps: [NOMBRE/EMAIL]
- Backend Lead: [NOMBRE/EMAIL]

---

**Última actualización:** 2025-11-17
