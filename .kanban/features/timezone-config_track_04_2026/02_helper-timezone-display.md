# 02 — Helper/Service `TimezoneDisplay`

**Track:** timezone-config
**Proyecto:** core
**Prioridad:** high
**Estado:** done
**Ref plan:** SF-02

## Qué hacer
- Crear `app/Common/Services/TimezoneDisplay.php`
- Método principal: `display(Carbon|string $date, string $format = 'd/m/Y H:i'): string`
- Resolución de timezone: itera guards configurados para encontrar el usuario activo → `user->timezone` → `tenant->timezone` → `config('app.timezone')` (UTC)
- **NUNCA llamar `date_default_timezone_set()`** — solo usar `->setTimezone()` en el objeto Carbon, nunca globalmente
- Registrar como helper global en `composer.json` → `autoload.files` → `app/helpers.php`

## Firma esperada
```php
function display_date(Carbon|string $date, string $format = 'd/m/Y H:i'): string
```

## Criterio de aceptación
- Un usuario sin timezone hereda la del tenant
- Un tenant sin timezone usa UTC
- No modifica `config('app.timezone')` en ningún momento
- Funciona correctamente con ambos guards (`landlord` y `web`)
