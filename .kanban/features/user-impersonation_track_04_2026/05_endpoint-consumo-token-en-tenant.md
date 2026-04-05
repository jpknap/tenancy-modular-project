# 05 — Endpoint consumo de token en Tenant

**Track:** user-impersonation
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Ruta manual en `routes/tenant.php`: `GET /system-access/{token}`. Valida token contra BD central (cross-connection explícita), login como system_user, redirige a pantalla de selección de usuario.
