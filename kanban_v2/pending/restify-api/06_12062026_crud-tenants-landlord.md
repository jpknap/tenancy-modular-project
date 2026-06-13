# 06 — CRUD Tenants (Landlord)

**Track:** restify-api
**Proyecto:** landlord
**Prioridad:** high
**Estado:** pending

## Qué hacer
`TenantRestifyRepository` con:
- Fields: `id`, `name`, `identifier`, `current_project`, `status`, `timezone`, `locale`, `data`, `created_at`
- Relations: `domains` (eager loading)
- Filters: `name`, `identifier`, `current_project`, `status`
- Rules delegando a `TenantFormRequest`
- Acciones: `switch-project`, `activate`, `deactivate`

## Criterio de aceptación
- CRUD de tenants funcional
- `GET /tenants/{id}?include=domains` incluye dominios anidados en la respuesta
