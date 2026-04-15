# dev_log — user-impersonation track

## Feature #015–#019 — Acceso a tenant como system_user desde landlord (2026-04-15)

**Decisiones:**
- `is_system_user` va directo en `create_users_table` (no ALTER separado) — mantiene migraciones atómicas
- Token HMAC-SHA256 stateless firmado con `app.key` — descartada tabla central `tenant_system_accounts` por complejidad innecesaria
- One-time use via `Cache` con TTL = tiempo restante del token — previene replay sin estado persistente
- `is_system_user` removido de `$fillable`; solo asignable via `forceFill` en seeders — previene mass assignment
- `SystemLoginController` en `app/Common/Http/Controller/` registrado como ruta manual en `routes/tenant.php` — no pasa por `EndpointProcessor` porque es cross-project
- Scheme derivado de `config('app.url')` — soporta HTTP y HTTPS sin cambio de código
- Sesión `landlord` y sesión `web` del tenant son completamente independientes (subdominios distintos = cookies distintas) — el `session()->regenerate()` es buena práctica pero el riesgo de session fixation cross-domain es nulo

**Deuda aceptada:**
- `canBeImpersonated()` definido en User models pero no invocado en `ImpersonationController` — issue pre-existente, pendiente de refactor en track de impersonation interna
- Clave HMAC dedicada (`SYSTEM_LOGIN_HMAC_KEY`) separada de `APP_KEY` — mejora futura, actualmente comparte la clave de cifrado de cookies

## Feature #020–#022 — Impersonación de usuarios dentro del tenant (2026-04-15)

**Decisiones:**
- Split en dos controllers (`ImpersonationController` + `StopImpersonationController`) porque `EndpointProcessor` suma middlewares de método y clase — no sobreescribe. El stop necesita solo `auth.tenant` (el suplantado no es system_user), el start necesita `auth.tenant + auth.system_user`.
- Session key `system_impersonator_id` (distinta de `impersonator_id` del landlord) — evita colisiones entre los dos flujos de impersonación.
- Route del banner resuelto dinámicamente via `ProjectManager::getCurrentProject()->getPrefix()` — soporta cualquier proyecto tenant futuro sin cambiar el layout.
- `canImpersonate()` invocado en el controller como defensa en profundidad, además del middleware `auth.system_user` — cierra la brecha si el middleware cambia de lugar en el futuro.
- `StopImpersonationController` valida que `system_impersonator_id` en sesión corresponda a un usuario con `is_system_user=true` antes de `loginUsingId()` — previene session hijack.
- `session()->regenerate()` en ambos controllers post-`loginUsingId()`.

**Deuda aceptada:**
- 0 tests del flujo de impersonación — la suite base ya tiene 5 fallas pre-existentes que bloquean agregar cobertura significativa.
- `canImpersonate()` / `canBeImpersonated()` definidos en ambos User models (central y ActivitiesBoard) sin extraer a un trait — código duplicado menor, pendiente de consolidar.

**Afecta features siguientes:**
- #020–#023 (impersonation interna dentro del tenant): dependen de `is_system_user=true` como punto de entrada al tenant. El `SystemLoginController` es el portal; desde ahí el system_user puede suplantar usuarios reales del tenant.
- El `TenantSystemUserSeeder` crea el usuario base que usarán las features de suplantación interna.
