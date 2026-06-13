# 01c — Cache de roles por usuario en cache store (TTL 10 min)

**Track:** permission-system
**Proyecto:** core
**Prioridad:** high
**Estado:** in-progress
**Depende de:** 01b (FlushPermissionCache + unique cache key por tenant) ✅

## Por qué es necesaria

Con la capa de cache global del tenant (01b) se evita recargar la definición de
roles y permisos desde BD. Pero spatie todavía hace **una query por request** para
cargar qué roles tiene asignados el usuario autenticado:

```sql
SELECT roles.* FROM model_has_roles
INNER JOIN roles ON roles.id = model_has_roles.role_id
WHERE model_id = {user_id} AND model_type = 'App\Projects\...\Models\User'
```

Con muchos usuarios navegando en simultáneo esto escala mal. La solución es
cachear esa asignación en el cache store con TTL corto (10 min), de modo que
durante la navegación normal no haya ninguna query a BD para chequeos de permisos.

**Por qué cache store y no sesión PHP:**
- La sesión no permite invalidación selectiva — si un admin cambia el rol de un
  usuario no hay forma de borrar solo ese dato de la sesión del afectado sin
  conocer su session ID.
- El cache store sí permite `cache()->forget("user.{id}.roles.tenant.{tid}")` al
  momento exacto del cambio.
- Driver `file` para MVP de un servidor → `redis` para escala horizontal: cero
  cambios de código, solo cambiar `CACHE_DRIVER` en `.env`.

## Qué hacer

### SF-01: Middleware `CacheUserRoles`

`app/Http/Middleware/CacheUserRoles.php` — nuevo

```
Flujo:
1. Resolver usuario autenticado iterando guards ['landlord', 'web']
2. Si no hay usuario autenticado → pasar sin hacer nada
3. Construir key: "user.{user_id}.roles.tenant.{tenant_id}"
4. cache()->remember(key, 10 min, fn → $user->roles()->with('permissions')->get())
5. $user->setRelation('roles', $cachedRoles)
   → Eloquent usa esta colección para el resto del request sin query adicional
```

Solo debe correr en contexto tenant (tenancy()->initialized === true).

### SF-02: Registrar middleware en bootstrap/app.php

Alias `cache.user.roles`, aplicado después de los middlewares de auth en las
rutas tenant. Registrar DESPUÉS de `InitializeTenancyByDomain`.

### SF-03: Invalidación en UserService (los tres proyectos)

Cuando se asigna o cambia el rol de un usuario:

```php
cache()->forget("user.{$user->id}.roles.tenant." . tenant()->getTenantKey());
```

Llamar en el método que persiste el rol (actualmente en el `update()` de
`UserService` de cada proyecto: ActivitiesBoard, SportCompetition, Landlord).

### SF-04: Test unitario del middleware

`tests/Unit/Permission/CacheUserRolesMiddlewareTest.php`

- Verifica que en el segundo call el cache store es hit (no DB query)
- Verifica que `setRelation('roles', ...)` fue llamado con la colección cacheada
- Verifica que si no hay usuario autenticado el middleware pasa sin error
- Verifica que en contexto no-tenant (landlord) el middleware no hace nada

## Criterio de aceptación

- Navegar 5 páginas del admin tenant genera 0 queries a `model_has_roles`
  después del primer request (verificable con `DB::enableQueryLog()`)
- Cambiar el rol de un usuario desde el admin invalida su cache inmediatamente
- Cambiar `CACHE_DRIVER=redis` en `.env` funciona sin tocar código
- Los tests pasan

## Archivos a crear/modificar

| Archivo | Cambio |
|---|---|
| `app/Http/Middleware/CacheUserRoles.php` | nuevo |
| `bootstrap/app.php` | registrar alias del middleware |
| `app/Projects/ActivitiesBoard/Services/Model/UserService.php` | invalidación |
| `app/Projects/SportCompetition/Services/Model/UserService.php` | invalidación |
| `app/Projects/Landlord/Services/Model/TenantService.php` o UserService | invalidación |
| `tests/Unit/Permission/CacheUserRolesMiddlewareTest.php` | nuevo |

## Notas de diseño

- `setRelation('roles', $collection)` es la API pública de Eloquent para
  pre-popular una relación — spatie la respeta porque accede via `$user->roles`
  que primero chequea si la relación ya está cargada.
- TTL 10 min es conservador: tolera que un admin tarde hasta 10 min en ver su
  nuevo rol si el cache no se invalida correctamente, pero en la práctica la
  invalidación explícita en UserService garantiza que sea inmediato.
- El middleware debe ser idempotente: si `$user->relationLoaded('roles')` ya
  es true (otro middleware lo cargó primero), no hace nada.

## Security Agent

ACTIVO — toca middleware de auth y el sistema de roles/permisos.
Riesgo principal: que el cache contamine roles entre usuarios si la key no es
suficientemente específica (user_id + tenant_id cubren esto).
