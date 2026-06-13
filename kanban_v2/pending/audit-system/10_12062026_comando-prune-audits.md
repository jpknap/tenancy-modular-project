# 10 — Comando artisan audit:prune + schedule

**Track:** audit-system
**Proyecto:** core
**Prioridad:** low
**Estado:** pending

## Qué hacer
Crear `app/Console/Commands/PruneAuditsCommand.php`:
- Opción `--days=90` (configurable)
- Itera todos los tenants con `$tenant->run(fn() => Audit::where('created_at', '<', now()->subDays($days))->delete())`
- Hard delete de registros antiguos

Schedulear mensualmente en `bootstrap/app.php`:
```php
Schedule::command('audit:prune --days=90')->monthly();
```

## Criterio de aceptación
- `php artisan audit:prune --days=30` elimina audits de más de 30 días en todos los tenants
- El comando es idempotente y no falla si un tenant no tiene audits
- El schedule aparece en `php artisan schedule:list`
