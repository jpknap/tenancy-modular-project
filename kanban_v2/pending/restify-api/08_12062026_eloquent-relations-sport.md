# 08 — Eloquent Relationships SportCompetition

**Track:** restify-api
**Proyecto:** sport-competition
**Prioridad:** medium
**Estado:** pending

## Qué hacer
Definir relaciones Eloquent en los modelos de SportCompetition (prerequisito de F9):

- `Competition` → `hasMany(GameMatch::class)`
- `Team` → `hasMany(Player::class)`
- `Player` → `belongsTo(Team::class)`
- `GameMatch` → `belongsTo(Competition::class)`, `homeTeam()`, `awayTeam()` (via `home_team_id`, `away_team_id`)

## Criterio de aceptación
- `Player::with('team')->first()` resuelve correctamente
- `GameMatch::with('homeTeam', 'awayTeam')->first()` resuelve correctamente
