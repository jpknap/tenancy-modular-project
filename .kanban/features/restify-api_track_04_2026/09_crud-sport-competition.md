# 09 — F9: CRUD SportCompetition (4 modelos)

**Track:** restify-api
**Proyecto:** sport-competition
**Prioridad:** medium
**Estado:** backlog

CRUD completo para los 4 modelos de SportCompetition con acciones de negocio.

**Repositories:**
- `CompetitionRestifyRepository` → actions: `start`, `finish`, `cancel`
- `TeamRestifyRepository` → relations: players
- `PlayerRestifyRepository` → relations: team
- `GameMatchRestifyRepository` → actions: `record-score`, `start-match`, `finish-match`

**Archivos clave:**
- `app/Projects/SportCompetition/Api/CompetitionRestifyRepository.php` (nuevo)
- `app/Projects/SportCompetition/Api/TeamRestifyRepository.php` (nuevo)
- `app/Projects/SportCompetition/Api/PlayerRestifyRepository.php` (nuevo)
- `app/Projects/SportCompetition/Api/GameMatchRestifyRepository.php` (nuevo)
- `app/Projects/SportCompetition/Providers/SportCompetitionServiceProvider.php` → registrar

**Criterio de aceptación:** `record-score` actualiza scores solo si status es `in_progress`. Relaciones incluibles via `?include=`.
