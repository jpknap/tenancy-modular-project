# 01b — Cache de permisos aislado por tenant (PermissionRegistrar)

**Track:** permission-system
**Proyecto:** core
**Prioridad:** high
**Estado:** ✅ done — 2026-06-12
**Commit:** `75044f6` en `feat/permission-system`

## Qué se hizo

### FlushPermissionCache listener (nuevo)
`app/Listeners/FlushPermissionCache.php`

Al inicializar tenancy:
1. Setea `PermissionRegistrar::cacheKey` a `spatie.permission.cache.tenant.{id}` — evita que dos tenants compartan la misma entrada en Redis/file cache
2. Llama `forgetCachedPermissions()` — limpia la cache in-memory para evitar bleeding en tenant-switch (flujo de impersonación)

### TenancyServiceProvider (modificado)
`app/Providers/TenancyServiceProvider.php`

Registrado `FlushPermissionCache::class` bajo `TenancyInitialized::class`, después de `ProjectInitializedListener`.

### Tests
`tests/Unit/Permission/FlushPermissionCacheTest.php` — 2 tests, 4 assertions.
- Verifica que el cacheKey cambia al ID del tenant correcto
- Verifica que cambia con cada tenant distinto

## Por qué era necesario

`spatie/laravel-permission` usa una clave de cache estática (`spatie.permission.cache`) y guarda permisos en memoria estática del `PermissionRegistrar`. En un entorno multi-tenant con cache store compartido (Redis) esto produce dos bugs:
1. **Cache bleeding**: Tenant B lee permisos del Tenant A porque usan la misma clave
2. **Stale in-memory**: Tras un tenant-switch (flujo de impersonación) los permisos en RAM son del tenant anterior

## Archivos modificados

- `app/Listeners/FlushPermissionCache.php` (nuevo)
- `app/Providers/TenancyServiceProvider.php` (+1 listener en TenancyInitialized)
- `tests/Unit/Permission/FlushPermissionCacheTest.php` (nuevo)
- `app/helpers.php` (restaurado — faltaba en la rama)
