# 03 — Crear system_user automático al crear Tenant

**Track:** user-impersonation
**Proyecto:** landlord
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `TenantSystemUserSeeder` corre en `setupDefaultSettings()` después del `RolesAndPermissionsSeeder`
- Crea user con `is_system_user=true`, email `system@internal`, password UUID auto-generado
- Password hasheado en el schema del tenant, almacenado encriptado en `tenant_system_accounts` (BD central)
