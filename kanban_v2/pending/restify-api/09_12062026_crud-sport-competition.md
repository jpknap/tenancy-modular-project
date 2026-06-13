# 09 — CRUD SportCompetition (4 modelos)

**Track:** restify-api
**Proyecto:** sport-competition
**Prioridad:** medium
**Estado:** pending

## Qué hacer
Repositories Restify para los 4 modelos con sus acciones de negocio:
- `CompetitionRestifyRepository` → acciones: `start`, `finish`, `cancel`
- `TeamRestifyRepository` → relations: players
- `PlayerRestifyRepository` → relations: team
- `GameMatchRestifyRepository` → acciones: `record-score`, `start-match`, `finish-match`

Registrar todos en `SportCompetitionServiceProvider`.

## Criterio de aceptación
- `record-score` actualiza scores solo si status es `in_progress`
- Relaciones incluibles via `?include=`
