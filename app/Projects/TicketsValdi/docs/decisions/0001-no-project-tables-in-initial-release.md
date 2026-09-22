# ADR-0001: Arrancar sin tablas propias del proyecto

- **Status**: Superseded — las 11 tablas del dominio se crearon una vez cerradas las
  preguntas de visibilidad, reserva de cupo y checkout de invitados
  (ver [ADR-0005](./0005-institution-scope-on-platform-users.md) y
  [ADR-0006](./0006-guest-checkout-with-deferred-registration.md))
- **Date**: 2026-09-21
- **Deciders**: JuanMunozBouchard

## Context

TicketsValdi se creó para que el proyecto fuera seleccionable al dar de alta un tenant,
antes de tener definido el dominio de tickets. Las reglas de negocio (estados de un
ticket, actores, asignación, SLA) todavía no están cerradas — ver
[DOMAIN.md § Open questions](../DOMAIN.md#open-questions).

La plataforma ya provee, vía `database/migrations/projects/Common/`, todo lo necesario
para que un tenant funcione: `users`, `cache`, `jobs`, tablas de spatie/laravel-permission,
`personal_access_tokens` y `activity_log`.

`MigrateProjectDatabase` corre primero `Common/` y después la carpeta del proyecto, así que
una carpeta propia vacía es un caso válido y no rompe el alta de tenants.

## Decision

`database/migrations/projects/TicketsValdi/` existe pero queda **vacía** (solo `.gitkeep`)
hasta que [DOMAIN.md](../DOMAIN.md) esté cerrado. Ninguna tabla del dominio se crea antes
de ese momento.

## Consequences

### Positive

- El proyecto es seleccionable y usable (login + ABM de usuarios) sin comprometer un diseño
  de datos que todavía no está validado.
- Evita migraciones de corrección temprana: una tabla mal modelada ya desplegada en varios
  tenants es caro de arreglar.
- La carpeta existe, así que sumar la primera migración no requiere tocar la clase del
  proyecto ni `getPathMigration()`.

### Negative

- El proyecto no hace nada específico de tickets: para un tenant real hoy es equivalente a
  un CRUD de usuarios.
- `.gitkeep` es necesario porque git no versiona directorios vacíos; es fácil borrarlo por
  descuido y dejar el proyecto sin carpeta de migraciones.

### Follow-up

- Cerrar [DOMAIN.md](../DOMAIN.md) traduciendo la estructura SQL base a `erDiagram`.
- Recién entonces crear la primera migración y correr
  `php artisan tenants:migrate-project --project=tickets-valdi`.

## Alternatives considered

| Alternativa | Por qué se descartó |
|---|---|
| Crear ya un `tickets` mínimo (id, title, status) | Adivinar el modelo y después migrarlo en tenants productivos es más caro que esperar |
| No crear la carpeta hasta tener la primera migración | `getPathMigration()` apuntaría a un directorio inexistente; el doc de migraciones lista ese caso como fuente de error |
| Reusar las tablas de ActivitiesBoard | Dominios distintos; acoplaría dos proyectos que deben ser independientes |
