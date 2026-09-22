# ADR-0004: Reusar `users` de la plataforma para los admins; `customers` aparte

- **Status**: Accepted
- **Date**: 2026-09-21
- **Deciders**: JuanMunozBouchard

## Context

El modelo de origen define dos tablas de personas con credenciales:

- `usuarios_admin` — operadores del panel, con columna `rol` (enum `rol_admin`:
  `super_admin`, `admin_institucion`, `validador`) y pivote `admin_institucion`.
- `clientes` — compradores de entradas, con `documento` y `telefono`.

La plataforma ya provee, en cada schema de tenant:

- la tabla `users` (guard `web`), con `name`, `email`, `password`, `enabled`, `timezone`,
  `locale`;
- **spatie/laravel-permission** instalado, con `roles`, `permissions` y sus pivotes.

Crear `usuarios_admin` duplicaría la autenticación ya resuelta, y una columna `rol` propia
duplicaría lo que spatie modela con más flexibilidad (un usuario con varios roles,
permisos granulares, caché de roles ya integrado en el repo vía `cache.user.roles`).

Los clientes son otra cosa: no entran al panel, tienen datos que `users` no modela y su
ciclo de vida es independiente.

## Decision

1. **`usuarios_admin` no se crea.** Los operadores del panel son la tabla `users` de la
   plataforma, autenticados con el guard `web` ([ADR-0002](./0002-use-web-guard-for-tenant-users.md)).
2. **El enum `rol_admin` no se crea como columna.** Los tres roles se registran como roles
   de spatie: `super_admin`, `institution_admin`, `validator`.
3. **El pivote `admin_institucion` sí se crea**, como `institution_user` — es información
   del dominio (qué usuario opera qué institución), no de autenticación.
4. **`customers` se crea como tabla propia** del dominio, con su propio `password`.

En el ER de [DOMAIN.md](../DOMAIN.md#er-diagram), `users` aparece reducida a las columnas
necesarias para anclar las FKs, marcada como *plataforma — no redefinir*.
`roles` y `permissions` no aparecen.

## Consequences

### Positive

- Una sola identidad para el panel: login, suplantación, auditoría (`activity_log`) y caché
  de roles funcionan sin código extra.
- Los roles son extensibles sin migración: agregar `finance_viewer` es un seeder, no un
  `ALTER TYPE`.
- Un mismo usuario puede acumular roles (admin de institución **y** validador), que el enum
  de una sola columna no permitía.

### Negative

- El modelo de datos deja de ser autocontenido: quien lea solo las migraciones del proyecto
  no ve de dónde salen los usuarios. Mitigado documentándolo en DOMAIN.md y acá.
- Dos mecanismos de autenticación conviviendo (`users` para el panel, `customers` para
  compradores). Hay que tener claro cuál aplica en cada ruta.
- El scoping por institución (BR-2) queda a cargo de la aplicación: spatie da el rol, pero
  no sabe qué instituciones tiene asignadas el usuario.

### Follow-up

- Definir el guard de `customers` — ver [DOMAIN.md § Open questions](../DOMAIN.md#open-questions)
  punto 3: ¿front público con sesión propia o compra como invitado?
- Seeder de los tres roles con sus permisos.
- Implementar el scoping por `institution_user` (¿global scope, policy, o filtro explícito
  en los repositorios?).

## Alternatives considered

| Alternativa | Por qué se descartó |
|---|---|
| Crear `usuarios_admin` tal cual el modelo original | Duplica autenticación ya resuelta y rompe la integración con impersonation, activity_log y caché de roles |
| Columna `role` en `users` en vez de spatie | El paquete ya está instalado y en uso; una columna no soporta multi-rol ni permisos granulares |
| Meter los `customers` en `users` con un flag | Semántica distinta, campos distintos y superficie de login distinta; un flag mezclaría dos audiencias en la misma tabla |
