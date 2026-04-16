# 06 — Trait Auditable en modelos tenant (Activity, User tenant)

**Track:** audit-system
**Proyecto:** all
**Prioridad:** high
**Estado:** backlog

Agregar `Auditable` trait a `app/Projects/ActivitiesBoard/Models/Activity.php` y a los `User.php` de cada proyecto tenant. Sin `$auditConnection` — heredan automáticamente el schema activo de stancl/tenancy.
