# 04 — Botón "Acceder al Tenant" en lista de tenants

**Track:** user-impersonation
**Proyecto:** landlord
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `TenantAccessController` en Landlord con verificación de superadmin
- `ListAction` con `target=_blank` y condition callable en `TenantAdmin::getListViewConfig()`
- Solo visible para usuarios con rol superadmin
