# 05 — F5: CRUD Users (Landlord)

**Track:** restify-api
**Proyecto:** landlord
**Prioridad:** high
**Estado:** backlog

Primer CRUD completo como referencia. `UserRestifyRepository` con fields (id, name, email, status, is_system_user, created_at), filters (name, email, status), rules delegando a `UserFormRequest` existente, acciones `activate` / `deactivate`. Registrar en `LandlordServiceProvider`.

**Archivos clave:**
- `app/Projects/Landlord/Api/UserRestifyRepository.php` (nuevo)
- `app/Projects/Landlord/Providers/LandlordServiceProvider.php` → registrar en boot

**Endpoints generados:**
- `GET/POST /landlord/api/users`
- `GET/PATCH/DELETE /landlord/api/users/{id}`
- `POST /landlord/api/users/{id}/actions/activate`
- `POST /landlord/api/users/{id}/actions/deactivate`

**Criterio de aceptación:** CRUD completo funcional. Filtro por email. Sin token → 401.
