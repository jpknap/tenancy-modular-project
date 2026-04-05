# 03 — Crear system_user automático al crear Tenant

**Track:** user-impersonation
**Proyecto:** landlord
**Prioridad:** high
**Estado:** backlog

En `TenantService::create()`, después de correr migraciones del tenant: conectar al schema del tenant, crear user con `is_system_user=true`, guardar password encriptado en `tenant_system_accounts`.
