# 07 — Crear modelos faltantes SportCompetition

**Track:** audit-system
**Proyecto:** sport-competition
**Prioridad:** medium
**Estado:** backlog

Crear archivos PHP para los modelos que existen solo en migraciones: `Competition.php`, `Team.php`, `Player.php`, `GameMatch.php` (con `$table = 'matches'` para evitar keyword `match`). Cada uno incluye `Auditable` trait, `$fillable` y relaciones. Registrar repositorios en `SportCompetitionServiceProvider`.
