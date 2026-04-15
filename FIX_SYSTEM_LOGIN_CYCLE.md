# Fix: Sistema Login Ciclo Infinito

## Problema Identificado

Cuando se accede desde el tenant landlord por system login, ocurría un ciclo infinito:
- `/sport-competition/auth/login` ↔ `/sport-competition/admin/users/list`

## Root Cause

Los logs revelaron que:
```
"guard":"landlord"
"is_authenticated":false
"auth_all_guards":{"web_check":true,"web_id":1}
```

El usuario se autenticaba correctamente con `guard='web'` pero el middleware verificaba con `guard='landlord'`.

**Causa:**
1. `SystemLoginController` autentica con: `Auth::guard('web')->loginUsingId($uid)`
2. `AdminController` requería: `#[Middleware(['auth.landlord'])]`
3. El middleware `EnsureAuthenticated` verificaba con el guard incorrecto

## Configuración de Guards

```php
// auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',  // Usa el modelo User
    ],
    'landlord' => [
        'driver' => 'session',
        'provider' => 'landlord_users',  // También usa el modelo User
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    'landlord_users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],
```

El problema: `landlord_users` está configurado de forma diferente y no funciona en contexto de tenancia.

## Solución

Cambiar `AdminController` (común a Landlord y Tenants) de usar `auth.landlord` a `auth.web`:

```php
// app/Common/Admin/Controller/AdminController.php
-#[Middleware(['auth.landlord'])]
+#[Middleware(['auth.web'])]
abstract class AdminController extends Controller
```

## Por qué funciona

1. **En Landlord** (sin tenancia):
   - `auth.web` usa el provider `users` con el modelo User
   - Sin tenancia inicializada, usa la conexión por defecto (landlord)
   - Los usuarios del landlord están en la tabla de usuarios central
   - ✅ Funciona correctamente

2. **En Tenant** (con tenancia inicializada):
   - El middleware `InitializeTenancyByDomain` cambia la conexión por defecto a la del tenant
   - `auth.web` usa el provider `users` con el modelo User
   - Ahora accede a los usuarios en la base de datos del tenant
   - ✅ Funciona correctamente

3. **System Login Flow**:
   - Usuario en Landlord genera token system login
   - `SystemLoginController::login()` autentica con `Auth::guard('web')` ✅
   - Redirige a `/sport-competition/admin/users/list` (tenant)
   - Middleware `EnsureAuthenticated` verifica `Auth::guard('web')` ✅
   - Usuario está autenticado, acceso permitido ✅

## Cambios Realizados

1. `app/Common/Admin/Controller/AdminController.php`
   - Cambio de `auth.landlord` a `auth.web`

## Notas

- Los controladores específicos de Landlord que requieren explícitamente `auth.landlord` (como `TenantAccessController`, `ImpersonationController`, `ProfileController`) mantienen su configuración
- Estos controladores no heredan el cambio porque especifican su propio middleware
- El cambio solo afecta a los AdminControllers (Landlord y Tenants) que heredan de `AdminController`
