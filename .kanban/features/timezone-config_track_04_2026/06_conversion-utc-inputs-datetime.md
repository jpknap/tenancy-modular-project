# 06 — Conversión UTC en inputs datetime de entidades

**Track:** timezone-config
**Proyecto:** activities-board
**Prioridad:** medium
**Estado:** backlog
**Ref plan:** SF-06
**Depende de:** 01, 02

## Qué hacer
- Revisar todos los `FormRequest` que tengan campos `date` o `datetime` (`ActivityFormRequest` y similares)
- Al recibir el valor del browser (`datetime-local` envía hora local del browser, NO UTC), convertir a UTC antes de persistir:
  ```php
  Carbon::createFromFormat('Y-m-d\TH:i', $request->datetime, $userTimezone)->utc()
  ```
- Actualizar `ActivityService::create()` y `ActivityService::update()` para aplicar la conversión
- En Jobs que serialicen fechas: asegurar que todo Carbon en jobs sea UTC explícito

## Consideración crítica
Los `<input type="datetime-local">` envían la hora en el timezone del browser del usuario, no en UTC. Sin esta conversión, las fechas se guardan desplazadas en BD.

## Criterio de aceptación
- Una actividad creada desde un browser en UTC-3 se guarda en BD como UTC
- Al mostrarse con `@displayDate`, aparece en la timezone configurada del usuario
- Los jobs que procesen fechas no tienen desplazamientos de timezone
