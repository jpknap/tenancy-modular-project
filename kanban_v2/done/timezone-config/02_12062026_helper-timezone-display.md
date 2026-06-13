# 02 — Helper/Service TimezoneDisplay

**Track:** timezone-config
**Proyecto:** core
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `app/Common/Services/TimezoneDisplay.php`
- Helper global `display_date(Carbon|string $date, string $format): string` registrado en `composer.json → autoload.files`
- Resolución: usuario activo → tenant → `config('app.timezone')` (UTC)
- **Nunca llama `date_default_timezone_set()`** — solo usa `->setTimezone()` en el Carbon
