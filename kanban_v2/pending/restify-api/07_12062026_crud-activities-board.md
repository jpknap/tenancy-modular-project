# 07 — CRUD Users + Activities (ActivitiesBoard)

**Track:** restify-api
**Proyecto:** activities-board
**Prioridad:** medium
**Estado:** pending

## Qué hacer
- `UserRestifyRepository` (mismo patrón que Landlord, modelo `ActivitiesBoard\Models\User`)
- `ActivityRestifyRepository` con fields (`id`, `name`, `description`, `created_at`), rules delegando a `ActivityFormRequest`
- Registrar ambos en `ActivitiesBoardServiceProvider`

## Criterio de aceptación
- CRUD activities funcional
- Tenant bleeding check: usuario de tenant A no ve datos de tenant B
