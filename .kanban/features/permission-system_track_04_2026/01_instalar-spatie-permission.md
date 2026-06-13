# 01 — Instalar spatie/laravel-permission + migraciones tenant

**Track:** permission-system
**Proyecto:** core
**Prioridad:** high
**Estado:** in-progress
**Security Agent:** ACTIVO — toca `TenancyServiceProvider.php` (archivo de tenancy/auth) y el subsistema de gates/permissions

## Descripción

Instalar y configurar `spatie/laravel-permission` para que las tablas de roles y permisos existan en el schema de cada tenant, con cache correcto en contexto multi-tenant (sin bleeding entre tenants).

## Estado de instalación (ya hecho)

- ✅ `spatie/laravel-permission ^8.0` en `composer.json` (instalado)
- ✅ `config/permission.php` publicado, `teams => false`
- ✅ Migración `2026_04_14_000000_create_permission_tables.php` en `database/migrations/projects/Common/` (path correcto — `MigrateProjectDatabase` siempre corre Common)

## Qué falta

El paquete funciona por tabla: roles/permissions se crean en el schema del tenant. Pero el cache de permisos es **compartido** (Redis/archivo) con clave estática `spatie.permission.cache`. Si dos tenants comparten el mismo cache store, una request de un tenant contamina el cache del otro. Además, el `PermissionRegistrar` guarda permisos en memoria — si hay un tenant switch (ej: Landlord accediendo a un tenant via sistema), la cache in-memory queda del tenant anterior.

## Sub-features

- SF-01: Listener `FlushPermissionCache` — al inicializar tenancy: setear clave de cache tenant-específica (`spatie.permission.cache.{tenant_id}`) y llamar `forgetCachedPermissions()` — **baja complejidad** — sin dependencias
- SF-02: Registrar `FlushPermissionCache` en `TenancyServiceProvider::events()` bajo `TenancyInitialized` — **muy baja complejidad** — depende de SF-01
- SF-03: Test `PermissionMigrationTest` — verificar que `roles`, `permissions` y tablas pivot existen luego de correr migraciones Common en tenant in-memory — **media complejidad** — paralelizable con SF-01/SF-02

## Decisiones arquitectónicas

- **No** usamos closure en `config/permission.php` porque la config se resuelve en boot, antes de que tenancy esté inicializada. El approach correcto es setear `PermissionRegistrar::cacheKey` dinámicamente en el listener.
- Se prefiere un listener dedicado `FlushPermissionCache` (no modificar `ProjectInitializedListener`) para mantener responsabilidad única.
- El listener debe vivir en `app/Listeners/` siguiendo el patrón del proyecto.
- Al terminar la tenancy (`TenancyEnded`) no es necesario resetear porque el contexto central no usa spatie/permission (no hay `HasRoles` en User central por ahora).

## Notas del dev_log

_(dev_log vacío — primera feature del track)_
