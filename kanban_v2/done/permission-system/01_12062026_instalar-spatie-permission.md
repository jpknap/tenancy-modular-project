# 01 — Instalar spatie/laravel-permission + migraciones tenant

**Track:** permission-system
**Proyecto:** core
**Prioridad:** high
**Estado:** ✅ done — 2026-06-12

## Qué se hizo

- `spatie/laravel-permission ^8.0` en `composer.json` e instalado
- `config/permission.php` publicado con `teams => false`
- Migración `2026_04_14_000000_create_permission_tables.php` movida a `database/migrations/projects/Common/`

## Por qué el path Common es correcto

`MigrateProjectDatabase` siempre incluye las migraciones de `database/migrations/projects/Common/` antes de las del proyecto específico. Así `roles`, `permissions` y tablas pivot se crean en cada schema de tenant, no en la BD central.

## Por qué teams => false

El aislamiento lo da el schema separado de stancl/tenancy. `teams` agregaría columna `team_id` innecesaria.

## Continúa en

Ver `01b` para el fix de cache bleeding entre tenants (segunda parte de esta instalación).
