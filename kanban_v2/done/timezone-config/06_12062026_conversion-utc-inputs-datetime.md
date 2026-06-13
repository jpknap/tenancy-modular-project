# 06 — Conversión UTC en inputs datetime de entidades

**Track:** timezone-config
**Proyecto:** activities-board
**Prioridad:** medium
**Estado:** done

## Qué se hizo
- `ActivityFormRequest` y similares convierten el datetime recibido del browser a UTC antes de persistir:
  ```php
  Carbon::createFromFormat('Y-m-d\TH:i', $request->datetime, $userTimezone)->utc()
  ```
- `ActivityService::create()` y `update()` aplican la conversión

## Contexto clave
Los `<input type="datetime-local">` envían hora local del browser, NO UTC. Sin esta conversión las fechas se guardan desplazadas en BD.
