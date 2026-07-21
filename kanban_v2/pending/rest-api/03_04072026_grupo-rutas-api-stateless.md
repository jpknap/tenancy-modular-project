# 03 — Grupo de rutas API stateless en web.php / tenant.php + errores JSON

**Track:** rest-api
**Proyecto:** core
**Prioridad:** high
**Estado:** pending

## Objetivo
- Nuevo grupo en `routes/tenant.php` **sin** middleware `web` (stateless): `InitializeTenancyByDomain` → `PreventAccessFromCentralDomains` → `ProjectInitialized` → `throttle:api`; `auth:sanctum` se aplica por endpoint vía `#[Middleware]` (login queda público)
- Grupo equivalente en `routes/web.php` con `EnsureIsCentralDomain` para el API del landlord
- El orden tenancy-antes-de-auth es el invariante crítico: documentarlo en el código
- Exception handler en `bootstrap/app.php`: respuestas JSON uniformes (`{ message, errors? }`) para 401/403/422 en rutas `*/api/*` — nunca redirect a login ni HTML

## Tests primero (red)
Escritos ANTES de tocar `web.php` / `tenant.php` / `bootstrap/app.php`:
1. Las rutas `{prefix}.api.auth.*` existen (central y tenant) — assert sobre `Route::has()`
2. Request sin token a endpoint protegido → 401 con body JSON `{ message }` (no redirect, no HTML)
3. 403 y 422 en rutas `*/api/*` también responden JSON uniforme
4. Los middleware del grupo aparecen en el orden tenancy → project → throttle (assert sobre `Route::getRoutes()`)

## Criterio de aceptación
- Los 4 tests en verde; `php artisan route:list` consistente

## Dependencias
- SF-02
