# Track — Configuración de Idioma (language-config)

## Estado actual
**Completado** — ver `done/language-config.md`.

## Descripción
Idioma configurable por usuario con fallback al tenant y luego al global. Middleware `SetLocale` aplica el locale en cada request antes de renderizar cualquier vista.

---

## Flujo
```
Request → Middleware SetLocale
    → Auth::user()->locale ?? tenant->locale ?? config('app.locale')
    → App::setLocale($locale)
```

## Decisiones de diseño
- **Nivel usuario, fallback tenant** — distintos usuarios del mismo tenant pueden tener idiomas distintos
- **No usar el campo `data` JSON del Tenant** — campo dedicado `locale VARCHAR(10)` es más claro y eficiente
- **SetLocale después de `InitializeTenancyByDomain`** — necesita el tenant activo para leer su locale
- **Guard dual** — el middleware itera `landlord` y `web` para obtener el usuario activo
