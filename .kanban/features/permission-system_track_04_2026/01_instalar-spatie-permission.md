# 01 — Instalar spatie/laravel-permission + migraciones tenant

**Track:** permission-system
**Proyecto:** core
**Prioridad:** high
**Estado:** in-progress

## Qué hacer
- `composer require spatie/laravel-permission`
- `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`
- Mover las migraciones publicadas al path de migraciones tenant (`database/migrations/projects/Common/`)
- Configurar `config/permission.php`:
  - `'teams' => false` (innecesario: schema separado por tenant ya aísla)
  - `'cache.key'` dinámico si hay conflictos entre tenants

## Criterio de aceptación
- `php artisan migrate:fresh` crea tablas `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` en cada schema de tenant
- No se crean en la BD central
