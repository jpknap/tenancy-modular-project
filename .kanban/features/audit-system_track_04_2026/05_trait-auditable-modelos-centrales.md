# 05 — Trait Auditable en modelos centrales (User, Tenant)

**Track:** audit-system
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Agregar `Auditable` trait a `app/Models/User.php` y `app/Models/Tenant.php`. Sin `$auditConnection` en ninguno — en contexto landlord la conexión activa ya es pgsql, no hace falta forzarla. En `User` excluir `password` y `remember_token` via `$auditExclude`.
