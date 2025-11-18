# Configuración de Claude Code Review en GitHub

## Estado Actual

✅ **Archivos configurados y subidos al repositorio:**
- [.github/workflows/claude.yml](.github/workflows/claude.yml) - GitHub Action para Claude
- [.clauderc](.clauderc) - Directivas de revisión de código del proyecto
- [.gitignore](.gitignore) - Actualizado para permitir versionar .clauderc

## Pasos Finales (Debes Completar)

### 1. Instalar Claude GitHub App

**URL:** https://github.com/apps/claude

**Pasos:**
1. Click en "Install" o "Configure"
2. Selecciona tu cuenta/organización: **AngelRubilar**
3. Selecciona el repositorio: **prueba-de-sqm**
4. Acepta los permisos solicitados:
   - ✅ Contents: Read & Write
   - ✅ Pull requests: Read & Write
   - ✅ Issues: Read & Write
5. Click en "Install"

### 2. Configurar ANTHROPIC_API_KEY

**URL:** https://github.com/AngelRubilar/prueba-de-sqm/settings/secrets/actions

**Pasos:**
1. Click en "New repository secret"
2. **Name:** `ANTHROPIC_API_KEY`
3. **Secret:** Tu API key de Anthropic
   - Obtén tu API key en: https://console.anthropic.com/settings/keys
4. Click en "Add secret"

### 3. Probar la Integración

**Opción A: Crear un Pull Request de prueba**

```bash
# Crear una rama de prueba
git checkout -b test/claude-review

# Hacer un cambio menor
echo "// Test Claude review" >> server/app.js

# Commit y push
git add server/app.js
git commit -m "test: Probar Claude code review"
git push origin test/claude-review

# Crear PR desde GitHub
gh pr create --title "Test: Claude Code Review" --body "Testing @claude integration"
```

**Opción B: Comentar en un PR existente**

Si ya tienes un PR abierto, simplemente comenta:

```
@claude por favor revisa este código
```

## Cómo Funciona

### Activación Automática

El workflow se activa cuando:
- Alguien menciona `@claude` en un comentario de PR
- Alguien menciona `@claude` en un comentario de issue
- Se edita un comentario que menciona `@claude`

### Qué Revisará Claude

Según las directivas en [.clauderc](.clauderc), Claude verificará:

**🔒 Seguridad Crítica:**
- ❌ Credenciales hardcodeadas
- ✅ Variables de entorno usadas correctamente
- ✅ Sin SQL injection, XSS, command injection
- ✅ Passwords con bcrypt
- ✅ CORS y headers de seguridad configurados

**🏗️ Arquitectura:**
- ✅ Separación de capas (Controllers → Services → Repositories)
- ✅ No mezclar responsabilidades
- ✅ Error handling con try/catch

**⚡ Performance:**
- ✅ Operaciones asíncronas (async/await)
- ✅ Sin operaciones bloqueantes
- ✅ Cache en Redis apropiado
- ✅ Timezone correcto (America/Santiago)

**📝 Logging:**
- ✅ Winston en lugar de console.log
- ✅ Niveles apropiados (info, warn, error)

### Rechazos Automáticos

Claude rechazará automáticamente código con:
- Credenciales en código
- SQL queries sin prepared statements
- Uso de `eval()` o `Function()`
- Dependencias con vulnerabilidades
- Código que bloquea el event loop
- Uso de `fs.writeFileSync()` para estado

## Ejemplos de Uso

### Pedir Revisión General

```
@claude por favor revisa este PR
```

### Pedir Revisión de Seguridad

```
@claude revisa la seguridad de estos cambios
```

### Pedir Ayuda con un Error

```
@claude los tests están fallando, puedes ayudar?
```

### Pedir Sugerencias de Mejora

```
@claude qué mejoras sugerirías para este código?
```

## Limitaciones

- Claude solo responde cuando es mencionado explícitamente con `@claude`
- Requiere que la GitHub App esté instalada
- Requiere que `ANTHROPIC_API_KEY` esté configurado en secrets
- Consume créditos de tu cuenta de Anthropic

## Verificar que Está Funcionando

1. **GitHub Actions:**
   - Ve a: https://github.com/AngelRubilar/prueba-de-sqm/actions
   - Deberías ver el workflow "Claude Code Review"

2. **Logs del Workflow:**
   - Después de mencionar @claude en un PR
   - Ve a Actions y busca la ejecución
   - Revisa los logs para ver qué hizo Claude

3. **Comentarios de Claude:**
   - Claude responderá directamente en el PR/Issue
   - Verás su análisis y recomendaciones

## Troubleshooting

### Claude no responde

- ✅ Verifica que la GitHub App esté instalada
- ✅ Verifica que `ANTHROPIC_API_KEY` esté en secrets
- ✅ Revisa los logs en GitHub Actions
- ✅ Asegúrate de mencionar exactamente `@claude`

### Error de permisos

- ✅ Reinstala la GitHub App
- ✅ Verifica que tenga permisos de Contents, PRs e Issues

### API Key inválida

- ✅ Genera nueva API key en console.anthropic.com
- ✅ Actualiza el secret `ANTHROPIC_API_KEY`

## Próximos Pasos

Una vez configurado, puedes:

1. **Usarlo en todos tus PRs** para revisión automática
2. **Pedir ayuda en issues** para debugging
3. **Solicitar sugerencias** de refactoring
4. **Verificar seguridad** antes de mergear

## Referencias

- **GitHub App:** https://github.com/apps/claude
- **Claude Code Action:** https://github.com/anthropics/claude-code-action
- **Documentación oficial:** https://support.claude.com/en/articles/10167454-using-the-github-integration
- **Anthropic Console:** https://console.anthropic.com/

---

**Última actualización:** 2025-11-17
