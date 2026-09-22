# ADR-0003: Identificadores `bigint` secuenciales, no UUID

- **Status**: Accepted
- **Date**: 2026-09-21
- **Deciders**: JuanMunozBouchard

## Context

El modelo de datos de origen (DBML) define `id uuid [pk]` en las 11 tablas del dominio.

La plataforma sobre la que corre TicketsValdi usa claves autoincrementales en todas sus
tablas: `users`, `tenants`, `domains`, las de spatie/laravel-permission y `activity_log`.
Las FKs del dominio hacia `users` (`institution_user.user_id`,
`ticket_checkins.validated_by`) tienen que coincidir en tipo con esa clave.

Laravel modela esto de fábrica: `$table->id()` genera `bigIncrements`, y
`$table->foreignId()` la contraparte.

## Decision

Todas las tablas del dominio usan **`bigint` autoincremental con secuencia** como clave
primaria. No se usa UUID en ninguna tabla.

## Consequences

### Positive

- Consistencia con la plataforma: las FKs hacia `users` no necesitan conversión de tipo.
- Índices más chicos y localidad de escritura: un UUIDv4 como PK genera inserciones
  aleatorias en el B-tree, con más fragmentación y páginas sucias.
- `$table->id()` / `foreignId()` sin configuración extra ni traits de UUID en los modelos.

### Negative

- Los IDs son **enumerables**: exponer `/events/123` filtra volumen de negocio y permite
  tantear registros vecinos. Mitigación disponible si hace falta: slug público o columna
  `public_id` en las entidades que se exponen a clientes.
- Los IDs se asignan al insertar, así que no se pueden generar del lado del cliente antes
  de persistir.

### Follow-up

- El QR del ticket **no debe llevar el `id`**: ya existen `qr_code` (único, público) y
  `qr_secret`. Ese par cubre el caso donde el UUID habría aportado opacidad.
- Si se expone una API pública de eventos, decidir ahí si hace falta `slug` o `public_id`.

## Alternatives considered

| Alternativa | Por qué se descartó |
|---|---|
| UUID v4 como PK (modelo original) | Inconsistente con la plataforma; peor rendimiento de índice; requiere traits en todos los modelos |
| ULID / UUID v7 (ordenables) | Resuelven la localidad de escritura pero siguen siendo inconsistentes con `users` y con el resto del repo |
| `bigint` PK + `uuid` público | Es la mitigación de reserva, no el punto de partida: suma una columna e índice por tabla sin necesidad demostrada |
