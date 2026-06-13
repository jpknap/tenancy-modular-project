# 01b — Cache de permisos aislado por tenant (PermissionRegistrar)

**Track:** permission-system
**Proyecto:** core
**Prioridad:** high
**Estado:** in-progress
**Bloqueada por:** 01 (instalación base) ✅
**Bloquea:** 02 (seeders), 03 (HasRoles) — no tiene sentido seedear permisos si el cache puede contaminar

## Por qué es necesaria esta tarjeta

`spatie/laravel-permission` cachea todos los permisos y roles en memoria (el `PermissionRegistrar`) **y** en el cache store de la app (Redis, archivo, etc.) con la clave estática `spatie.permission.cache`.

En una app multi-tenant con schemas separados esto genera dos problemas:

### Problema 1: Cache key compartida (bleeding entre tenants)

Si el cache store es Redis (o cualquier store compartido), la clave `spatie.permission.cache` es la misma para todos los tenants. Cuando el Tenant A carga sus permisos al cache, el Tenant B leerá esos mismos permisos en lugar de los suyos.

**Fix:** Setear `PermissionRegistrar::$cacheKey` dinámicamente con el ID del tenant al inicializar tenancy, ej: `spatie.permission.cache.tenant.{id}`.

### Problema 2: Cache in-memory entre requests (tenant switch)

El sistema de impersonación implementado en el track `user-impersonation` permite que un usuario del landlord ingrese a un tenant. En ese flujo hay un "switch" de contexto de tenant dentro de la misma sesión PHP. El `PermissionRegistrar` guarda los permisos en memoria estática — si no se limpia entre switches, la request del Tenant B usará permisos cargados del Tenant A.

**Fix:** Llamar `PermissionRegistrar::forgetCachedPermissions()` en el evento `TenancyInitialized`.

## Qué hacer

### SF-01: Listener FlushPermissionCache

Crear `app/Listeners/FlushPermissionCache.php`:

```php
class FlushPermissionCache
{
    public function handle(TenancyInitialized $event): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->cacheKey = 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->getTenantKey();
        $registrar->forgetCachedPermissions();
    }
}
```

### SF-02: Registrar en TenancyServiceProvider

En `app/Providers/TenancyServiceProvider.php`, bajo `TenancyInitialized::class`:

```php
Events\TenancyInitialized::class => [
    Listeners\BootstrapTenancy::class,
    ProjectInitializedListener::class,
    FlushPermissionCache::class,   // ← agregar
],
```

### SF-03: Test de aislamiento

Crear `tests/Feature/Permission/PermissionCacheIsolationTest.php`:
- Inicializar dos tenants distintos
- Seedear roles diferentes en cada uno
- Verificar que el tenant B no ve roles del tenant A después del switch

## Criterio de aceptación

- Con dos tenants activos, los roles/permisos de cada uno son independientes
- `PermissionRegistrar::cacheKey` cambia con cada cambio de contexto de tenant
- Los tests de aislamiento pasan en SQLite in-memory
- No hay error en `php artisan config:cache` (la clave del config/permission.php permanece estática; el cambio es en runtime)

## Archivos modificados

- `app/Listeners/FlushPermissionCache.php` (nuevo)
- `app/Providers/TenancyServiceProvider.php` (modificado: +1 listener)
- `tests/Feature/Permission/PermissionCacheIsolationTest.php` (nuevo)

## Notas de seguridad

Esta tarjeta activa el **Security Agent** porque toca `TenancyServiceProvider.php` (archivo de tenancy/auth) y el sistema de gates/permissions. El riesgo principal es que un error en el listener provoque que los permisos de un tenant queden vacíos o se mezclen — ambos casos son exploitables.
