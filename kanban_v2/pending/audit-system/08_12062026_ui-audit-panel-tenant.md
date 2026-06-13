# 08 — UI Audit panel en tenant (ActivitiesBoard + SportCompetition)

**Track:** audit-system
**Proyecto:** all
**Prioridad:** medium
**Estado:** pending

## Qué hacer
Crear `AuditController` en cada proyecto tenant con:
- `GET /admin/audits` — lista paginada de audits del tenant
- `GET /admin/audits/{id}` — vista detalle con diff old/new values

Usar atributos `#[Route]` / `#[RoutePrefix]` como el resto de controladores. Read-only, no usa `AdminController` genérico.

Las vistas blade muestran: modelo auditado, acción (created/updated/deleted), usuario, fecha, y tabla comparativa old → new.

## Criterio de aceptación
- Lista de audits paginada y accesible solo para admin/superadmin
- Vista detalle muestra diferencias old/new correctamente
- No se puede crear ni editar audits desde la UI
