# 10 — F10: Authorization con Spatie + Policies

**Track:** restify-api
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Crear Laravel Policies por modelo que delegan a permisos Spatie (`users:list`, `users:create`, etc.). Registrar en `AppServiceProvider` via `Gate::policy()`. Cada `RestifyBaseRepository` llama `authorizeToXxx()` que delega a la policy. Seeder de permisos por proyecto.

**Archivos clave (nuevos):**
- `app/Policies/UserPolicy.php`
- `app/Policies/TenantPolicy.php`
- `app/Policies/ActivityPolicy.php`
- `app/Policies/CompetitionPolicy.php`, `TeamPolicy.php`, `PlayerPolicy.php`, `GameMatchPolicy.php`
- `database/seeders/PermissionsSeeder.php`
- `app/Providers/AppServiceProvider.php` → `Gate::policy()`

**Criterio de aceptación:** Usuario sin permiso → 403. Superadmin (Gate::before) → siempre pasa.
