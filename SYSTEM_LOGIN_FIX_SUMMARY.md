# Sistema Login - Resumen de Diagnóstico y Fix

## 🎯 Problema Original
Ciclo infinito al acceder por system login desde landlord:
- `/sport-competition/auth/login` ↔ `/sport-competition/admin/users/list`
- Logs mostraban ~500ms por ciclo

## 🔍 Diagnóstico (Logs Instrumentados)

Se agregaron logs exhaustivos en 6 puntos clave del sistema:

### Logs Agregados:
1. **SystemLoginController** - Autentica usuario
2. **EnsureAuthenticated** - Valida autenticación en middleware
3. **ProjectManager** - Gestiona proyectos actuales
4. **ProjectInitService** - Inicializa proyecto y tenancia
5. **ProjectInitialized** - Ejecuta ProjectInitService
6. **LogTenancyState** - Monitor de estado de tenancia

Todos los logs usan el prefijo `[Log-System-Auth]` para fácil filtrado.

### Hallazgo Clave:
```json
{
  "path": "sport-competition/admin/users/list",
  "guard": "landlord",
  "is_authenticated": false,
  "auth_all_guards": {
    "web_check": true,
    "web_id": 1
  }
}
```

**Interpretación:**
- Usuario autenticado con `guard='web'` ✅
- Pero middleware verificaba con `guard='landlord'` ❌
- Guard `landlord` no encontraba el usuario en contexto de tenancia

## 🔧 Root Cause

El `AdminController` (heredado por todos los admin controllers):
```php
#[Middleware(['auth.landlord'])]  // ← PROBLEMA
abstract class AdminController extends Controller
```

**Por qué causaba el problema:**
1. `SystemLoginController` → `Auth::guard('web')->loginUsingId($uid)` ✅
2. Redirige a `/sport-competition/admin/users/list` 
3. Middleware `EnsureAuthenticated` con `guard='landlord'` busca usuario ❌
4. No lo encuentra (está autenticado en `guard='web'`, no `landlord`)
5. Redirige a `/sport-competition/auth/login`
6. Vuelve a `/sport-competition/admin/users/list` → Ciclo infinito

## ✅ Fix Aplicado

```diff
# app/Common/Admin/Controller/AdminController.php
- #[Middleware(['auth.landlord'])]
+ #[Middleware(['auth.web'])]
abstract class AdminController extends Controller
```

**Por qué funciona:**

| Contexto | Guard | Provider | Conexión | Resultado |
|----------|-------|----------|----------|-----------|
| **Landlord** (sin tenancia) | `web` | `users` | default (landlord) | ✅ Usuarios del landlord |
| **Tenant** (con tenancia) | `web` | `users` | default (tenant por middleware) | ✅ Usuarios del tenant |

El middleware de tenancia cambia automáticamente la conexión por defecto según el contexto, permitiendo que `auth.web` funcione correctamente en ambos casos.

## 📁 Archivos Modificados

### Cambios Críticos (Para Fix):
- `app/Common/Admin/Controller/AdminController.php` - Guard: `auth.landlord` → `auth.web`

### Cambios para Debugging (Logs Instrumentados):
- `app/Common/Http/Controller/SystemLoginController.php` - Logs detallados de autenticación
- `app/Http/Middleware/EnsureAuthenticated.php` - Logs de verificación de guards
- `app/ProjectManager.php` - Logs de gestión de proyectos
- `app/Services/ProjectInitService.php` - Logs de inicialización
- `app/Http/Middleware/ProjectInitialized.php` - Logs de middleware
- `app/Http/Middleware/LogTenancyState.php` - **NUEVO** - Monitor de tenancia
- `routes/tenant.php` - Agrega LogTenancyState a rutas

### Archivos de Referencia:
- `LOG_DEBUG_GUIDE.md` - Guía de cómo analizar los logs
- `filter_logs.sh` - Script para filtrar logs
- `FIX_SYSTEM_LOGIN_CYCLE.md` - Documentación técnica del fix

## 🚀 Cómo Probar

### 1. Limpiar y reiniciar servidor
```bash
# Terminal 1: Servidor
composer dev

# Terminal 2: Logs en tiempo real
tail -f storage/logs/laravel.log | grep '\[Log-System-Auth\]'
```

### 2. Reproducer el flujo
1. Acceder a `/landlord/admin/tenants` como superadmin
2. Click en "Acceso al Sistema" para un tenant
3. Se genera token system login (URL con ?uid=X&exp=Y&sig=Z)
4. Se redirige automáticamente a `/sport-competition/admin/users/list`

### 3. Validar el fix
**Logs esperados (SIN CICLO):**
```
SystemLoginController::login() iniciado
Parámetros recibidos: uid, exp
Usuario encontrado: id=1, is_system_user=true
Autenticando usuario en guard web
Verificando autenticación: is_authenticated=true ✅
Redirigiendo a: /sport-competition/admin/users/list

EnsureAuthenticated::handle()
  guard=web
  is_authenticated=true ✅
  auth_user_id=1
```

## 🧪 Notas sobre Guards

- `auth.landlord`: Guard específico para landlord (mantiene providers diferentes por razones de diseño)
- `auth.web`: Guard genérico que se adapta al contexto (landlord o tenant)

Controllers específicos de Landlord que requieren `auth.landlord`:
- `TenantAccessController` 
- `ImpersonationController`
- `ProfileController`

Estos mantienen su configuración `auth.landlord` y no son afectados por este fix.

## 📊 Impacto

- **Líneas cambiadas:** 1 (guard en AdminController)
- **Archivos afectados:** 1 principal, 6 de debugging
- **Compatibilidad:** Retrocompatible (mismo modelo de usuario)
- **Risk:** Mínimo (guards comparten el mismo modelo)
