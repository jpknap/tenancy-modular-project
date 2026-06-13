# Track — API REST con Restify (restify-api)

## Estado actual
**Pendiente** — no iniciado.

## Descripción
Exponer una API REST completa usando `binaryk/laravel-restify` con autenticación Sanctum. Cada proyecto tiene sus propios endpoints bajo `/{project}/api`. La misma estructura modular del sistema web (proyectos, repositories, ServiceProviders) se reutiliza para la capa API.

---

## Arquitectura

### URL pattern
```
GET  /landlord/api/users
GET  /activities-board/api/activities
GET  /sport-competition/api/competitions
```

### Middleware stack
```
InitializeTenancyByDomain → ApiProjectContext → auth:sanctum → recursos
```

`ApiProjectContext` resuelve el proyecto desde el path (igual que `ProjectInitService` pero para API).

### RestifyBaseRepository
Clase abstracta que integra Restify con `RepositoryManager`:
- `repository()` resuelve el `BaseRepository` del proyecto activo
- `newQueryWithoutScopes()` pasa por `getQueryBuilder()` del `BaseRepository`
- `fields()` y `rules()` abstractos (cada repository los implementa)

### Autenticación
- Sanctum tokens (Bearer)
- Login/logout/me en `BaseAuthApiController`
- Rutas de auth públicas, resto protegidas con `auth:sanctum`

---

## Sub-features

| # | Feature | Archivo | Prioridad |
|---|---------|---------|-----------|
| #045 | Setup Restify + Sanctum | `pending/restify-api/01_*` | high |
| #046 | Middleware ApiProjectContext | `pending/restify-api/02_*` | high |
| #047 | Auth API Login/Logout | `pending/restify-api/03_*` | high |
| #048 | RestifyBaseRepository | `pending/restify-api/04_*` | high |
| #049 | CRUD Users Landlord | `pending/restify-api/05_*` | high |
| #050 | CRUD Tenants Landlord | `pending/restify-api/06_*` | high |
| #051 | CRUD Users + Activities ActivitiesBoard | `pending/restify-api/07_*` | medium |
| #052 | Eloquent Relations SportCompetition | `pending/restify-api/08_*` | medium |
| #053 | CRUD SportCompetition 4 modelos | `pending/restify-api/09_*` | medium |
| #054 | Authorization Policies + Spatie | `pending/restify-api/10_*` | high |
| #055 | OpenAPI Documentación | `pending/restify-api/11_*` | low |
| #056 | Feature Tests API | `pending/restify-api/12_*` | high |

## MVP recomendado
#045 → #046 → #047 → #048 → #049 → #050 → #054 → #056

## Dependencias
- `permission-system` (SF-01 a SF-03) debe estar completo antes de #054 (Policies + Spatie)
