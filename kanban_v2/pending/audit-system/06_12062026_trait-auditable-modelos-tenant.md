# 06 — Trait Auditable en modelos tenant (Activity, User tenant)

**Track:** audit-system
**Proyecto:** all
**Prioridad:** high
**Estado:** pending

## Qué hacer
Agregar `Auditable` trait a los modelos que viven en schemas de tenant:
- `app/Projects/ActivitiesBoard/Models/Activity.php`
- `app/Projects/ActivitiesBoard/Models/User.php`
- `app/Projects/SportCompetition/Models/User.php`

Sin `$auditConnection` — heredan automáticamente el schema activo de stancl/tenancy.

## Criterio de aceptación
- Crear/editar/eliminar una `Activity` genera registro en `audits` del schema del tenant
- Sin contaminación cross-tenant (cada tenant solo ve sus propios audits)
