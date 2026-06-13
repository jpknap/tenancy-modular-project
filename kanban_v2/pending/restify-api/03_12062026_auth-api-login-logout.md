# 03 — Auth API Login/Logout Sanctum

**Track:** restify-api
**Proyecto:** core
**Prioridad:** high
**Estado:** pending

## Qué hacer
`BaseAuthApiController` con:
- `POST /auth/login` → retorna token Sanctum
- `POST /auth/logout` → revoca token activo
- `GET /auth/me` → retorna usuario autenticado

Cada proyecto tiene su `AuthApiController` heredando del base con su guard (`landlord` o `web`). Rutas de auth públicas, rutas de recursos protegidas con `auth:sanctum`.

## Archivos clave (nuevos)
- `app/Common/Api/BaseAuthApiController.php`
- `app/Projects/Landlord/Http/Controller/Api/AuthApiController.php`
- `app/Projects/ActivitiesBoard/Http/Controller/Api/AuthApiController.php`
- `app/Projects/SportCompetition/Http/Controller/Api/AuthApiController.php`

## Criterio de aceptación
- POST login con credenciales válidas → `{ "token": "..." }`
- GET /me con Bearer → user autenticado
- GET /me sin token → 401
