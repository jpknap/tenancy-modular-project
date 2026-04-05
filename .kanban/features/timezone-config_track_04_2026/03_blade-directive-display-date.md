# 03 — Blade directive `@displayDate`

**Track:** timezone-config
**Proyecto:** core
**Prioridad:** medium
**Estado:** backlog
**Ref plan:** SF-03
**Depende de:** 02

## Qué hacer
- Registrar la directiva en `AppServiceProvider::boot()`:
  ```php
  Blade::directive('displayDate', fn($expr) => "<?= display_date($expr) ?>");
  ```
- Aplicar en todas las vistas que muestren fechas (list views de admin, profile, etc.)

## Uso esperado en vistas
```blade
@displayDate($activity->created_at)
@displayDate($activity->created_at, 'd/m/Y')
@displayDate($user->created_at, 'd/m/Y H:i')
```

## Criterio de aceptación
- La directiva renderiza la fecha en la timezone del usuario activo
- Si el helper `display_date` no está disponible (helper no cargado), lanza error claro
- Reemplaza cualquier uso directo de `->format()` en vistas que muestren fechas al usuario
