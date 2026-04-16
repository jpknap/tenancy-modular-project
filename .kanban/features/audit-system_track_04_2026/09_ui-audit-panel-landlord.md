# 09 — UI Audit cross-tenant en panel Landlord

**Track:** audit-system
**Proyecto:** landlord
**Prioridad:** medium
**Estado:** backlog

Crear `AuditController` en Landlord que usa `$tenant->run()` para consultar la tabla `audits` de cualquier tenant. Selector de tenant, tabla paginada y vista detalle con old/new values. Incluye audits del modelo `Tenant` (DB central).
