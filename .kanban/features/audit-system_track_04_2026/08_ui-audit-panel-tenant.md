# 08 — UI Audit panel en tenant (ActivitiesBoard + SportCompetition)

**Track:** audit-system
**Proyecto:** all
**Prioridad:** medium
**Estado:** backlog

Crear `AuditController` en cada proyecto tenant con rutas `audit.list` y `audit.show` usando `#[Route]` attributes. Vistas blade con tabla paginada y vista detalle con diff old/new values. Read-only, no usa AdminController CRUD genérico.
