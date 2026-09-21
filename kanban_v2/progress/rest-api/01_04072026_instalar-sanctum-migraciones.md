# 01 — Instalar Sanctum + migraciones central/tenant + guard

**Track:** rest-api
**Proyecto:** core (Common)
**Prioridad:** high
**Estado:** in-progress
**Security Agent:** ACTIVO — toca `App\Models\User` (auth), emisión de tokens y aislamiento multi-tenant de `personal_access_tokens` (riesgo de tenant bleeding si el guard o el orden de middleware quedan mal).

## Descripción
Dejar operativo Laravel Sanctum como mecanismo de autenticación por token para la futura API REST, compatible con la arquitectura DB-per-tenant de `stancl/tenancy`. Es la base del track `rest-api`; sin esto no puede arrancar ninguna feature de auth ni, más adelante, la capa de recursos (fuera de alcance de este track).

## Estado al iniciar (verificado 19/07/2026)
Ya existe trabajo previo sin terminar:
- ✅ Tests "red" ya escritos: `tests/Feature/Api/SanctumSetupTest.php` + `tests/Support/CreatesTenants.php`
- ✅ Migración `personal_access_tokens` creada en central (`database/migrations/`) y en tenant (`database/migrations/projects/Common/`)
- ✅ `config/sanctum.php` publicado (stub estándar, `expiration => null`)
- ❌ `laravel/sanctum` NO está en `composer.json`/`composer.lock`, aunque el paquete (v4.3.2) está físicamente en `vendor/` de este entorno local — hay que reconciliar con `composer require`
- ❌ `App\Models\User` no tiene el trait `HasApiTokens` → `createToken()` no existe todavía
- Resultado actual de la suite: 2/4 tests en verde (los que solo chequean `Schema::hasTable`), 2/4 rojos (los que llaman `createToken()`)

## Sub-features
- SF-01: `composer require laravel/sanctum` para reconciliar composer.json/lock con el paquete ya presente en vendor — trivial — sin dependencias
- SF-02: agregar trait `HasApiTokens` a `App\Models\User` — trivial — depende de SF-01
- SF-03: confirmar que `config/sanctum.php` y las migraciones existentes cumplen el criterio de aceptación (no se toca `expiration` salvo que el track lo pida — hoy no hay decisión documentada, se deja `null`) — trivial — paralelizable con SF-02
- SF-04: correr suite completa (`SanctumSetupTest` + phpstan nivel 5 + ECS) y dejar los 4 tests en verde — depende de SF-02 y SF-03

## Decisiones arquitectónicas
- No se crea guard `api` en `config/auth.php` — se usa `auth:sanctum` directo sobre el guard `sanctum` que registra el propio Service Provider de Sanctum, según lo ya decidido en `kanban_v2/tracks/rest-api.md`.
- No se toca `bootstrap/app.php` en esta feature — no hace falta alias de middleware nuevo para esta sub-feature puntual (el grupo de rutas API stateless es la SF-03 del track, feature separada: `03_04072026_grupo-rutas-api-stateless.md`).

## Notas del dev_log
No existe `dev_log.md` en el track todavía (es la primera feature que se cierra).
