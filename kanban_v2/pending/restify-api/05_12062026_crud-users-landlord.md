# 05 — CRUD Users (Landlord)

**Track:** restify-api
**Proyecto:** landlord
**Prioridad:** high
**Estado:** pending

## Qué hacer
Primer CRUD completo de referencia. `UserRestifyRepository` con:
- Fields: `id`, `name`, `email`, `status`, `is_system_user`, `created_at`
- Filters: `name`, `email`, `status`
- Rules delegando a `UserFormRequest` existente
- Acciones: `activate`, `deactivate`

Registrar en `LandlordServiceProvider`.

## Endpoints generados
```
GET/POST    /landlord/api/users
GET/PATCH/DELETE /landlord/api/users/{id}
POST        /landlord/api/users/{id}/actions/activate
POST        /landlord/api/users/{id}/actions/deactivate
```

## Criterio de aceptación
- CRUD completo funcional con token válido
- Filtro por email funciona
- Sin token → 401
