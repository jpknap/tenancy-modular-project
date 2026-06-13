# 09 — UI Audit cross-tenant en panel Landlord

**Track:** audit-system
**Proyecto:** landlord
**Prioridad:** medium
**Estado:** pending

## Qué hacer
Crear `AuditController` en Landlord que:
- Usa `$tenant->run(fn() => Audit::paginate())` para consultar audits de cualquier tenant
- Selector de tenant (dropdown o filtro en URL)
- Tabla paginada con detalle
- Incluye audits del modelo `Tenant` (BD central)

## Criterio de aceptación
- Superadmin puede ver audits de cualquier tenant desde Landlord
- Los audits de cambios en `Tenant` aparecen en la vista landlord
- No se mezclan audits entre tenants
