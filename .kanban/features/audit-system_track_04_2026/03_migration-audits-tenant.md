# 03 — Migration tabla audits en schemas tenant

**Track:** audit-system
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Crear migration en `database/migrations/projects/Common/` para tabla `audits`. Se ejecuta automáticamente en todos los tenants al crear o migrar. Incluir índices compuestos en `(auditable_type, auditable_id, created_at)` y `(user_type, user_id, created_at)`.
