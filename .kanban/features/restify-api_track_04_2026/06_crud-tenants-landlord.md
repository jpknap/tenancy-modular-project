# 06 — F6: CRUD Tenants (Landlord)

**Track:** restify-api
**Proyecto:** landlord
**Prioridad:** high
**Estado:** backlog

`TenantRestifyRepository` con fields (id, name, identifier, current_project, status, timezone, locale, data, created_at), relations (domains eager loading), filters (name, identifier, current_project, status), rules delegando a `TenantFormRequest`. Acciones: `switch-project`, `activate`, `deactivate`.

**Archivos clave:**
- `app/Projects/Landlord/Api/TenantRestifyRepository.php` (nuevo)
- `app/Projects/Landlord/Providers/LandlordServiceProvider.php` → registrar

**Criterio de aceptación:** CRUD de tenants. `GET /tenants/{id}?include=domains` incluye dominios anidados.
