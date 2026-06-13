# 03 — Blade directive @displayDate

**Track:** timezone-config
**Proyecto:** core
**Prioridad:** medium
**Estado:** done

## Qué se hizo
Registrada en `AppServiceProvider::boot()`:
```php
Blade::directive('displayDate', fn($expr) => "<?= display_date($expr) ?>");
```

Uso en vistas:
```blade
@displayDate($activity->created_at)
@displayDate($activity->created_at, 'd/m/Y')
```
