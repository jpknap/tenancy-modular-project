# 10 — Authorization con Spatie + Policies

**Track:** restify-api
**Proyecto:** core
**Prioridad:** high
**Estado:** pending

## Dependencias
Requiere track `permission-system` completado (SF-01 a SF-03).

## Qué hacer
Crear Laravel Policies por modelo que delegan a permisos Spatie. Registrar en `AppServiceProvider` via `Gate::policy()`.

**Policies a crear:**
- `app/Policies/UserPolicy.php`
- `app/Policies/TenantPolicy.php`
- `app/Policies/ActivityPolicy.php`
- `app/Policies/CompetitionPolicy.php`, `TeamPolicy.php`, `PlayerPolicy.php`, `GameMatchPolicy.php`

**Seeder:**
- `database/seeders/PermissionsSeeder.php`

## Criterio de aceptación
- Usuario sin permiso → 403
- Superadmin (Gate::before) → siempre pasa sin tener el permiso asignado
