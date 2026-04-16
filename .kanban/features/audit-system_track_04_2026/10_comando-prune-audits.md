# 10 — Comando artisan audit:prune + schedule

**Track:** audit-system
**Proyecto:** core
**Prioridad:** low
**Estado:** backlog

Crear `app/Console/Commands/PruneAuditsCommand.php` con opción `--days=90`. Itera todos los tenants con `$tenant->run()` y hace hard delete de registros antiguos. Schedulear mensualmente en `bootstrap/app.php`.
