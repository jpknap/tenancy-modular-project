# Guía de Debug para Ciclo de System Login

## Problema
Cuando se accede desde el tenant landlord por system login, ocurre un ciclo infinito:
1. `/sport-competition/auth/login` 
2. `/sport-competition/admin/users/list`
3. Vuelve a `/sport-competition/auth/login`

## Flujo Esperado
1. `GET /system-login?uid=X&exp=Y&sig=Z` (desde landlord)
2. SystemLoginController autentica al usuario
3. Redirige a `/sport-competition/admin/users/list`
4. El usuario debe estar autenticado y acceder normalmente

## Puntos de Log Agregados

### 1. SystemLoginController::login() [Log-System-Auth]
**Archivos**: `app/Common/Http/Controller/SystemLoginController.php`

Verifica:
- ✅ `uid_param` recibido correctamente
- ✅ `Validación de firma` exitosa
- ✅ Usuario encontrado y es system_user
- ✅ `Autenticando usuario en guard web` (user_id)
- ✅ `Verificando autenticación post-login` (is_authenticated=true)
- ✅ Proyecto actual encontrado
- ✅ Ruta de destino existe

**Si falla aquí**: El usuario no se está autenticando correctamente

### 2. EnsureAuthenticated::handle() [Log-System-Auth]
**Archivos**: `app/Http/Middleware/EnsureAuthenticated.php`

Verifica:
- ✅ `is_authenticated` cuando llega a `/sport-competition/admin/users/list`
- ✅ `auth_user_id` está presente
- ⚠️ Si `is_authenticated=false` → PROBLEMA CRÍTICO

**Si falla aquí**: La sesión del usuario NO está siendo reconocida cuando llega al tenant

### 3. ProjectInitialized::handle() [Log-System-Auth]
**Archivos**: `app/Http/Middleware/ProjectInitialized.php`

Verifica:
- ✅ Middleware se ejecuta antes de AdminController
- ✅ Inicialización del proyecto completada

### 4. ProjectInitService::init() [Log-System-Auth]
**Archivos**: `app/Services/ProjectInitService.php`

Verifica:
- ✅ `tenancy_initialized` status
- ✅ Si es tenant: `tenant_id`, `tenant_domain`, `tenant_current_project`
- ✅ Proyecto obtenido correctamente

### 5. ProjectManager [Log-System-Auth]
**Archivos**: `app/ProjectManager.php`

Verifica:
- ✅ `setCurrentProject()` se llamó
- ✅ `getCurrentProject()` retorna el proyecto correcto
- ⚠️ Si `already_set=true` en setCurrentProject → podría ser problema

## Cómo Analizar Logs

```bash
# Ver todos los logs de system auth
tail -f storage/logs/laravel.log | grep '\[Log-System-Auth\]'

# Ver en orden cronológico
grep '\[Log-System-Auth\]' storage/logs/laravel.log | head -50
```

## Hipótesis Principales

### Hipótesis 1: Sesión no se comparte entre landlord y tenant
El usuario se autentica en landlord (`/system-login`), pero cuando se accede a `/sport-competition/admin/users/list` (tenant), la sesión no se reconoce.

**Síntoma**: En `EnsureAuthenticated::handle()`, verás `is_authenticated=false` cuando llega de `/sport-competition/admin/users/list`

**Solución potencial**: Revisar configuración de sesión, cookies, dominio

### Hipótesis 2: Middleware de tenancia interfiere con autenticación
El middleware `InitializeTenancyByDomain` podría estar limpiando o reiniciando la sesión

**Síntoma**: Ver logs de ProjectInitService que muestren comportamiento inesperado

**Solución potencial**: Revisar orden de middlewares en `routes/tenant.php`

### Hipótesis 3: Guard incorrecto en AdminController
El `AdminController` usa `auth.landlord` pero podría necesitar un guard diferente para tenants

**Síntoma**: En logs, guard='landlord' cuando debería ser 'web'

**Solución potencial**: Revisar configuración del guard en AdminController

## Próximos Pasos

1. Ejecuta el dev server: `composer dev`
2. Accede a `/system-login?uid=X&exp=Y&sig=Z`
3. Monitorea los logs en tiempo real
4. Compara el flujo esperado con el actual
5. Usa los logs para identificar dónde se pierde la autenticación
