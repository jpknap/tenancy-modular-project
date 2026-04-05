# 02 — Middleware SetLocale

**Track:** language-config
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Crear `app/Http/Middleware/SetLocale.php`. Resuelve locale: usuario → tenant → config fallback. Registrar en grupo `web` después de `InitializeTenancyByDomain`.
