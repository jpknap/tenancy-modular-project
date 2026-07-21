# 02 — BaseApiAuthController: login / logout / me con tokens

**Track:** rest-api
**Proyecto:** core (Common) + Landlord + ActivitiesBoard
**Prioridad:** high
**Estado:** in-progress
**Security Agent:** ACTIVO — controller de login (password, rate limit), emisión/revocación de tokens, chequeo de `enabled`, guards duales.

## Descripción
Exponer login/logout/me por HTTP con tokens Sanctum, imitando el patrón template-method de `BaseAuthController` (sesión) pero stateless. Es lo que permite probar el track con curl/Postman por primera vez.

## Contexto real del repo (verificado, no asumido)
- `BaseAuthController` (`app/Common/Http/Controller/Auth/BaseAuthController.php`): template method con `#[RoutePrefix('auth')]`, métodos abstractos `guard()`, `loginView()`, `defaultRedirectRoute()`, `loginRoute()`; `login()` valida inline con `$request->validate()` y usa `Auth::guard($this->guard())->attempt()` + `session()->regenerate()`.
- Controllers concretos: `Landlord\...\AuthController` (`guard() => 'landlord'`), `ActivitiesBoard\...\AuthController` (`guard() => 'web'`). No declaran `#[Route]` propios — heredan los del padre; `EndpointProcessor` recorre la cadena de herencia (`app/Services/EndpointProcessor.php:92-126`).
- `config/projects.php`: cada proyecto tiene `'controllers' => [...]`, leído por `{Name}Project.php` y pasado a `EndpointProcessor`.
- **No hay precedente de `throttle`** en el proyecto — se introduce desde cero.
- **No existe FormRequest de login** — el patrón actual valida inline. `BaseFormRequest` está acoplado al Admin/CRUD builder, no sirve para esto; se crea un `FormRequest` plano nuevo (no extiende `BaseFormRequest`).
- `bootstrap/app.php` → `withExceptions()` está vacío, sin manejo JSON custom todavía (eso es explícitamente parte de la feature 03, no de esta).
- No existe `routes/api.php`; las rutas se registran vía `Project::getEndpoints()` dentro de grupos de middleware en `web.php`/`tenant.php`.

## Decisión de scope — solapamiento con feature 03
El test #7 de esta feature ("la respuesta de login no setea cookies ni crea sesión") **exige que estas rutas específicas NO pasen por el middleware `web`** (que arranca sesión). Eso significa que esta feature ya necesita un grupo de rutas mínimamente stateless para `api/auth/*`, aunque el diseño *completo* del grupo API (manejo uniforme de errores JSON en `bootstrap/app.php`, throttle:api genérico para futuros recursos) sigue siendo el alcance de la **feature 03**. Se crea acá lo mínimo indispensable para que `api/auth/*` sea stateless; la feature 03 lo generaliza/revisita para el resto de la API. Esto se documenta para no duplicar trabajo cuando se aborde 03.

## Sub-features (TDD estricto: tests antes que implementación)
- SF-01: escribir los 7 tests rojos en `tests/Feature/Api/ApiAuthTest.php` (login válido, credenciales inválidas, `enabled=false`, logout revoca token, `me`, rate limit, sin cookies/sesión) — depende de feature 01 (ya hecha)
- SF-02: `BaseApiAuthController` abstracto (`login`/`logout`/`me`) + `LoginRequest` (FormRequest plano, no `BaseFormRequest`) en `app/Common/Http/Controller/Api/` — depende de SF-01
- SF-03: controllers concretos `Landlord\Http\Controller\Api\AuthController` (guard `landlord`) y `ActivitiesBoard\Http\Controller\Api\AuthController` (guard `web`), con `#[RoutePrefix('api/auth')]`, registrados en `config/projects.php`; grupo de rutas mínimo stateless en `web.php`/`tenant.php` para este prefijo (sin middleware `web`) — depende de SF-02
- SF-04: rate limiting del login (`RateLimiter::for('login', ...)` por email+IP, aplicado vía `#[Middleware(['throttle:login'])]` en el método `login`) — depende de SF-03
- SF-05: correr suite completa + phpstan nivel 5 + ECS, los 7 tests en verde — depende de SF-04

## Criterio de aceptación
Los 7 tests en verde; todo stateless: sin sesión, sin cookies, sin CSRF.

## Dependencias
- Feature 01 (Sanctum instalado) — completada
