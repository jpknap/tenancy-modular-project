# 07 — F7: CRUD Users + Activities (ActivitiesBoard)

**Track:** restify-api
**Proyecto:** activities-board
**Prioridad:** medium
**Estado:** backlog

`UserRestifyRepository` (mismo patrón que Landlord pero con modelo `ActivitiesBoard\Models\User`). `ActivityRestifyRepository` con fields (id, name, description, created_at), rules delegando a `ActivityFormRequest`. Registrar ambos en `ActivitiesBoardServiceProvider`.

**Archivos clave:**
- `app/Projects/ActivitiesBoard/Api/UserRestifyRepository.php` (nuevo)
- `app/Projects/ActivitiesBoard/Api/ActivityRestifyRepository.php` (nuevo)
- `app/Projects/ActivitiesBoard/Providers/ActivitiesBoardServiceProvider.php` → registrar

**Criterio de aceptación:** CRUD activities. Tenant bleeding check: usuario de tenant A no ve datos de tenant B.
