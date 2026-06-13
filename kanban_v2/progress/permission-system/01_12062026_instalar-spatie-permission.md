# 01 — Instalar spatie/laravel-permission + migraciones tenant

**Track:** permission-system
**Proyecto:** core
**Prioridad:** high
**Estado:** ✅ completado en instalación — ver 01b para lo pendiente

## Qué se hizo (ya en el repo)

- `spatie/laravel-permission ^8.0` en `composer.json` e instalado
- `config/permission.php` publicado con `teams => false`
- Migración `2026_04_14_000000_create_permission_tables.php` movida a `database/migrations/projects/Common/`

## Por qué el path Common es correcto

`MigrateProjectDatabase` (job que corre al crear un tenant) siempre incluye primero las migraciones de `database/migrations/projects/Common/` antes de las migraciones del proyecto específico. Así `roles`, `permissions` y tablas pivot se crean en **cada schema de tenant**, no en la BD central.

## Por qué teams => false

El aislamiento entre tenants lo da el schema separado de stancl/tenancy — no el feature "teams" de Spatie. Activar teams agregaría una columna `team_id` innecesaria y complicaría las queries.

## Lo que quedó pendiente → tarjeta 01b

La instalación base está lista pero el cache de permisos tiene un problema de bleeding multi-tenant que requiere trabajo adicional. Ver `01b_12062026_permission-cache-tenant-isolation.md`.

## Criterio de aceptación original

- `php artisan migrate:fresh` crea las 5 tablas en cada schema de tenant
- No se crean en la BD central
