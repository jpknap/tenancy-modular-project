# Track — Configuración de Zona Horaria (timezone-config)

## Estado actual
**Completado** — ver `done/timezone-config.md`.

## Descripción
BD y lógica siempre en UTC. Cada usuario/tenant configura su timezone, y las fechas se convierten solo en capa de presentación.

---

## Regla de oro
```
Guardar:  Carbon::now('UTC') → siempre UTC en BD
Mostrar:  $date->copy()->setTimezone($userTimezone)->format('d/m/Y H:i')
```

## Decisiones de diseño críticas
- **NUNCA llamar `date_default_timezone_set()` en Middleware** — rompe timestamps de Eloquent y es global
- **Conversión solo en presentación** — el helper `TimezoneDisplay` o directive `@displayDate` convierten en el blade, no antes
- **Inputs datetime del browser** — envían local time, no UTC. Convertir a UTC en el FormRequest antes de guardar
- **Jobs en queue** — siempre serializar fechas en UTC explícito, no en timezone de usuario
