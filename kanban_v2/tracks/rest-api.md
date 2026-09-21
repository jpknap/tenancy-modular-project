# Track — Autenticación API con Sanctum (rest-api)

## Estado actual
**Pending (04/07/2026)** — track definido, sin features iniciadas.

> **Alcance (recortado el 04/07/2026):** este track cubre **solo la autenticación API** (Sanctum). La generación de endpoints de recursos (atributo `#[ApiResource]`, controller genérico, etc.) se sacó de alcance y será un track futuro que dependerá de este.

## Descripción

Autenticación por tokens para consumir la aplicación vía API, sobre la arquitectura existente (atributos PHP 8 + `EndpointProcessor`), compatible con multi-tenancy DB-per-tenant de `stancl/tenancy`.

## Metodología — TDD

El track se desarrolla con **TDD estricto** (red → green → refactor):

1. **Red**: cada sub-feature arranca escribiendo los tests que definen su comportamiento (listados en la sección "Tests primero" de cada feature). Se corren y deben fallar por la razón correcta.
2. **Green**: se implementa lo mínimo para ponerlos en verde.
3. **Refactor**: con la suite verde, se limpia el diseño (phpstan nivel 5 + ECS incluidos).

Doble loop: SF-04 define los **tests de aceptación e2e** (flujo completo + aislamiento entre tenants). Se pueden escribir al inicio del track como suite fallando/skipped y actúan como criterio de cierre — el track está terminado cuando pasan sin `skip`.

Infra de tests: SQLite in-memory (`DB_CONNECTION=sqlite`, `TENANCY_CENTRAL_CONNECTION=sqlite`), `php artisan test --filter=` para el ciclo corto.

---

## Decisión — Laravel Sanctum (tokens personales)

### Estructura de tablas relevante

- `users` tiene **el mismo schema en central y en cada tenant** (`database/migrations/0001_01_01_000000_create_users_table.php` y `database/migrations/projects/Common/...`): `id, name, lastname, email (unique), password, enabled, timezone, locale`.
- Auth actual es 100% sesión: guards `web` (provider `users`) y `landlord` (provider `landlord_users`), ambos sobre `App\Models\User`.

### Por qué Sanctum funciona con DB-per-tenant

La tabla `personal_access_tokens` se crea **en central Y en `database/migrations/projects/Common/`** (cada tenant tiene la suya). El aislamiento es por construcción:

```
Request → InitializeTenancyByDomain (cambia conexión DB al tenant)
        → auth:sanctum (busca el token EN LA DB DEL TENANT)
        → token de tenant A jamás autentica en tenant B ni en central
```

La única condición es el **orden de middleware** — tenancy primero, auth después — y eso lo controlamos porque el grupo de rutas API es propio (ver Registro de rutas).

- **Modo token puro, stateless**: sin `EnsureFrontendRequestsAreStateful`, sin cookies, sin CSRF.
- Expiración vía `config('sanctum.expiration')`; revocación = borrar fila.
- Descartados: Passport (OAuth2, overkill), JWT tymon (revocación requiere infra extra, mantenimiento dudoso).
- Sanctum **no genera endpoints de recursos** — solo emite y valida tokens. La capa de recursos es un problema aparte (track futuro).

### Flujo de login

```
POST {projectPrefix}/api/auth/login   { email, password, device_name }
    → throttle (rate limit por email+IP)
    → valida credenciales + users.enabled = true
    → $user->createToken($device_name, $abilities)
    → 200 { token: "1|...", user: {...} }

POST {projectPrefix}/api/auth/logout  → revoca el token actual (auth:sanctum)
GET  {projectPrefix}/api/auth/me      → usuario autenticado (auth:sanctum)
```

Patrón igual a `BaseAuthController`: un `BaseApiAuthController` abstracto en `app/Common/Http/Controller/Api/` (template method), cada proyecto lo extiende solo para aportar prefijo/guard. Sirve tanto para landlord (dominio central) como para tenants — el provider resuelve `User` contra la conexión activa.

### Registro de rutas (grupo API propio, stateless)

Los controllers de auth API usan los atributos existentes (`#[RoutePrefix('api/auth')]`, `#[Route]`) y se registran en `config/projects.php` como cualquier controller. Lo nuevo es el **grupo** en `routes/web.php` y `routes/tenant.php`, junto a los existentes pero **sin** middleware `web`:

```php
// routes/tenant.php — nuevo grupo stateless
Route::middleware([
    InitializeTenancyByDomain::class,        // ① tenancy PRIMERO
    PreventAccessFromCentralDomains::class,
    ProjectInitialized::class,               // ② proyecto activo
    'throttle:api',                          // ③ rate limit
    // auth:sanctum se aplica por-endpoint via #[Middleware] (login es público)
])->group(function () { /* registro idéntico al grupo actual */ });
```

En `web.php` el grupo equivalente usa `EnsureIsCentralDomain` (API del landlord). El orden tenancy-antes-de-auth es el invariante crítico del track.

Manejo de errores: respuestas JSON uniformes (`{ message, errors? }`) para 401/403/422 cuando la ruta matchea `*/api/*` — exception handler en `bootstrap/app.php`.

---

## Sub-features

| # | Feature | Estado | Notas |
|---|---------|--------|-------|
| #01 | Instalar Sanctum + migraciones central y tenant + config | ⏳ pending | Base de todo el track — TDD: tests de migración/aislamiento primero |
| #02 | `BaseApiAuthController` — login / logout / me con tokens | ⏳ pending | Depende de #01 — TDD: feature tests de login primero |
| #03 | Grupo de rutas API stateless en `web.php` / `tenant.php` + errores JSON | ⏳ pending | Depende de #02 — TDD: asserts de rutas y 401 JSON primero |
| #04 | Suite de aceptación e2e: flujo completo + aislamiento entre tenants | ⏳ pending | Outer loop TDD — puede escribirse al inicio (skipped) como criterio de cierre |

## Deuda técnica anticipada

- **Track futuro — endpoints de recursos**: atributo `#[ApiResource]` en modelos + `ApiEndpointProcessor` + `ApiController` genérico sobre `RepositoryManager` (diseño descartado de este track el 04/07/2026; recuperable del historial git si se retoma).
- Token abilities / scopes por permiso spatie — cuando exista la capa de recursos.
- Rate limiting por tenant (no solo por IP).
- Limpieza de tokens expirados (`sanctum:prune-expired` en scheduler).
- Versionado (`/api/v1/`) antes de exponer a consumidores externos.
