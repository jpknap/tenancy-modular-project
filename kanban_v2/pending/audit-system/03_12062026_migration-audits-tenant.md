# 03 — Migration tabla audits en schemas tenant

**Track:** audit-system
**Proyecto:** core
**Prioridad:** high
**Estado:** pending

## Qué hacer
Crear migration en `database/migrations/projects/Common/` para tabla `audits`. Se ejecuta en todos los schemas de tenant al correr `php artisan tenants:migrate`.

Índices compuestos requeridos:
- `(auditable_type, auditable_id, created_at)`
- `(user_type, user_id, created_at)`

## Criterio de aceptación
- `php artisan tenants:migrate` crea tabla `audits` en cada schema de tenant
- La tabla no se crea en la BD central
- Los índices compuestos existen para performance en queries de auditoría
