# 03 — F3: Auth API Login/Logout Sanctum

**Track:** restify-api
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

`BaseAuthApiController` con endpoints `POST /auth/login` (retorna token Sanctum), `POST /auth/logout` (revoca token), `GET /auth/me`. Cada proyecto tiene su `AuthApiController` heredando del base con su guard (`landlord` o `web`). Rutas de auth públicas, rutas de recursos protegidas con `auth:sanctum`.

**Archivos clave:**
- `app/Common/Api/BaseAuthApiController.php` (nuevo)
- `app/Projects/Landlord/Http/Controller/Api/AuthApiController.php` (nuevo)
- `app/Projects/ActivitiesBoard/Http/Controller/Api/AuthApiController.php` (nuevo)
- `app/Projects/SportCompetition/Http/Controller/Api/AuthApiController.php` (nuevo)

**Criterio de aceptación:** POST login → `{ "token": "..." }`. GET /me con Bearer → user. GET /me sin token → 401.
