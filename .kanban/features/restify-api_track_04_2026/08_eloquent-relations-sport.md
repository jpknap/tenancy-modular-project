# 08 — F8: Eloquent Relationships SportCompetition

**Track:** restify-api
**Proyecto:** sport-competition
**Prioridad:** medium
**Estado:** backlog

Definir relaciones Eloquent faltantes en los modelos de SportCompetition (prerequisito de F9). Solo relaciones, sin lógica de negocio.

**Cambios:**
- `Competition` → `hasMany(GameMatch::class)`
- `Team` → `hasMany(Player::class)`
- `Player` → `belongsTo(Team::class)`
- `GameMatch` → `belongsTo(Competition::class)`, `homeTeam()`, `awayTeam()` (via `home_team_id`, `away_team_id`)

**Archivos clave:**
- `app/Projects/SportCompetition/Models/Competition.php`
- `app/Projects/SportCompetition/Models/Team.php`
- `app/Projects/SportCompetition/Models/Player.php`
- `app/Projects/SportCompetition/Models/GameMatch.php`

**Criterio de aceptación:** `Player::with('team')->first()` resuelve correctamente.
