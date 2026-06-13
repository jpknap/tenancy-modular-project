# 04 — Migration tabla audits en BD central

**Track:** audit-system
**Proyecto:** landlord
**Prioridad:** high
**Estado:** pending

## Qué hacer
Crear migration en `database/migrations/` (raíz, BD central) para tabla `audits`. Necesaria para trackear cambios al modelo `Tenant` que vive en la BD central.

Misma estructura que la migration tenant (mismos campos e índices).

## Criterio de aceptación
- `php artisan migrate` crea tabla `audits` en la BD central
- Los cambios al modelo `Tenant` (crear, editar, eliminar) quedan registrados en esta tabla
