# 07 — Crear modelos faltantes SportCompetition

**Track:** audit-system
**Proyecto:** sport-competition
**Prioridad:** medium
**Estado:** pending

## Qué hacer
Crear los modelos que existen solo en migraciones:
- `Competition.php` — `$fillable`, relaciones, Auditable
- `Team.php` — `$fillable`, relaciones, Auditable
- `Player.php` — `$fillable`, relaciones, Auditable
- `GameMatch.php` — `protected $table = 'matches'` (keyword PHP), relaciones, Auditable

Registrar los repositorios correspondientes en `SportCompetitionServiceProvider`.

## Criterio de aceptación
- Los 4 modelos existen y son instanciables
- Cambios a estos modelos quedan auditados
- `SportCompetitionServiceProvider` resuelve correctamente sus repositorios
