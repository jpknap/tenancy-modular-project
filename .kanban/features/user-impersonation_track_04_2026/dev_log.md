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

**Afecta features siguientes:**
- #020–#023 (impersonation interna dentro del tenant): dependen de `is_system_user=true` como punto de entrada al tenant. El `SystemLoginController` es el portal; desde ahí el system_user puede suplantar usuarios reales del tenant.
- El `TenantSystemUserSeeder` crea el usuario base que usarán las features de suplantación interna.
