# 02 — ImpersonationController (SportCompetition & Landlord)

**Track:** impersonation
**Proyecto:** sport-competition, landlord
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `ImpersonationController` creado en ambos proyectos bajo `Http/Controller/Admin/`
- Rutas generadas vía atributos PHP:
  - `POST admin/users/{id}/impersonate` → `{project}.admin.users.impersonate`
  - `POST admin/users/stop-impersonation` → `{project}.admin.users.stop-impersonation`
- `impersonate()`:
  - Gate::authorize('users:impersonate') — aborta 403 si no tiene permiso
  - Guarda `impersonator_id` en sesión
  - `Auth::loginUsingId($targetId)`
- `stopImpersonation()`:
  - Restaura al usuario original desde `session('impersonator_id')`
  - Limpia la sesión
- Controladores registrados en `config/projects.php`
- Rutas añadidas a `Enums/Routes.php` de cada proyecto

## Consideraciones
- El superadmin no puede suplantarse a sí mismo (validación explícita)
- Si `impersonator_id` no existe en sesión, `stopImpersonation` redirige sin error
