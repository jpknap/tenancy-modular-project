# 04 — Suite de aceptación e2e: flujo completo + aislamiento entre tenants

**Track:** rest-api
**Proyecto:** core + ActivitiesBoard
**Prioridad:** high
**Estado:** pending

## Objetivo
Outer loop del TDD del track: suite de aceptación que define "terminado". **Se recomienda escribirla al inicio del track** (marcada `skipped`/incomplete) e ir quitando skips a medida que SF-01→03 la ponen en verde.

- Flujo completo e2e: login → token → `me` → logout (token revocado → 401)
- Token expirado (`sanctum.expiration`) → 401
- **Aislamiento multi-tenant**: token emitido en tenant A → request al dominio de tenant B devuelve 401; token de tenant no autentica en central (landlord) ni viceversa
- El flujo completo no crea sesión ni setea cookies en ninguna respuesta

## Criterio de aceptación
- Suite verde con SQLite in-memory (`TENANCY_CENTRAL_CONNECTION=sqlite`)
- phpstan nivel 5 y ECS sin errores

## Dependencias
- SF-01 a SF-03
