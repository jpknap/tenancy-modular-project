# 09 — Log de auditoría de suplantaciones

**Track:** user-impersonation
**Proyecto:** landlord
**Prioridad:** low
**Estado:** backlog

Tabla central `impersonation_logs`: admin_id, tenant_id, target_user_id, started_at, ended_at, ip. Vista solo lectura en `/landlord/admin/impersonation-logs`.
