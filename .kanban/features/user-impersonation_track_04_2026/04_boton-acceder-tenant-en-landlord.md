# 04 — Botón "Acceder al Tenant" en lista de tenants

**Track:** user-impersonation
**Proyecto:** landlord
**Prioridad:** high
**Estado:** backlog

Acción por fila en `TenantAdmin::getListViewConfig()`. POST a `/landlord/impersonation/enter/{tenantId}`. Genera token de un solo uso (TTL 60s), redirige al dominio del tenant.
