# ADR-0002: Autenticar con el guard `web`, no con `landlord`

- **Status**: Accepted
- **Date**: 2026-09-21
- **Deciders**: JuanMunozBouchard

## Context

La aplicación define dos guards en `config/auth.php`:

- `web` → provider `users`, que resuelve contra la tabla `users` **del schema del tenant**
- `landlord` → provider `landlord_users`, la tabla de usuarios **del dominio central**

Los proyectos existentes no son consistentes: SportCompetition autentica con `web` en su
`AuthController` pero su `ImpersonationController` usa el guard `landlord` y el middleware
`auth.landlord` — una mezcla heredada del Landlord que produce un comportamiento ambiguo.
ActivitiesBoard, el proyecto más reciente, usa `web` de punta a punta con los middleware
`auth.tenant` / `auth.system_user`.

TicketsValdi corre en subdominios de tenant, donde los usuarios son del tenant.

## Decision

TicketsValdi usa **`web` de forma consistente** en todos sus controllers. El guard
`landlord` no se usa en este proyecto.

Cuando se sume suplantación de usuarios, se sigue el patrón de ActivitiesBoard
(`Auth::guard('web')` + middleware `auth.tenant` / `auth.system_user`), no el de
SportCompetition.

## Consequences

### Positive

- Coherencia entre login, autorización y suplantación: un solo guard, una sola sesión.
- Aislamiento correcto: un usuario del tenant nunca se autentica contra la tabla del
  dominio central.
- Alineado con el proyecto más reciente del repo, que es el que refleja la dirección actual.

### Negative

- Diverge de SportCompetition, así que copiar código de ese proyecto requiere revisar el
  guard antes de pegar.

### Follow-up

- Al implementar suplantación, portar `ImpersonationController` y
  `StopImpersonationController` desde ActivitiesBoard, no desde SportCompetition.

## Alternatives considered

| Alternativa | Por qué se descartó |
|---|---|
| Copiar SportCompetition tal cual (`web` + `auth.landlord` mezclados) | Replica un bug: exige sesión de landlord para operar sobre usuarios del tenant |
| Guard propio `tickets_valdi` | Sin beneficio: el provider sería idéntico a `users`, y sumaría configuración a mantener |
