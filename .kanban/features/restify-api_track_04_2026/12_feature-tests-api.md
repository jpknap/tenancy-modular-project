# 12 — F12: Feature Tests API

**Track:** restify-api
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Test coverage básica de la capa API. Todos los tests usan SQLite in-memory + `RefreshDatabase` + `Sanctum::actingAs()`.

**Tests a crear:**
- `tests/Feature/Api/AuthApiTest.php` — login, logout, me, 401 sin token
- `tests/Feature/Api/UserApiTest.php` — CRUD completo Landlord
- `tests/Feature/Api/ActivityApiTest.php` — CRUD ActivitiesBoard
- `tests/Feature/Api/SportCompetitionApiTest.php` — CRUD + record-score
- `tests/Feature/Api/TenantBleedingTest.php` — tenant A no ve datos de tenant B

**Criterio de aceptación:** `composer test` verde en los 12+ nuevos tests. TenantBleedingTest confirma aislamiento por schema PostgreSQL.
