# 02 — Token HMAC para acceso cross-domain al tenant

**Track:** user-impersonation
**Proyecto:** landlord
**Prioridad:** high
**Estado:** done

## Qué se hizo
- Token HMAC-SHA256 firmado con `app.key`, TTL 2 minutos
- One-time use via Cache (driver `database` — necesario para multi-tenant con stancl/tenancy)
- Guardado hasheado en Cache para validación sin exponer el token original
