# 02 — Middleware SetLocale

**Track:** language-config
**Proyecto:** core
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `app/Http/Middleware/SetLocale.php` — resuelve locale: usuario → tenant → config fallback
- Registrado en grupo `web` después de `InitializeTenancyByDomain`
- Itera guards `landlord` y `web` para obtener el usuario activo en ambos contextos
