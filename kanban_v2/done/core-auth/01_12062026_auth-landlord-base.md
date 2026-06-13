# 01 — Autenticación Landlord base compatible multi-proyecto

**Track:** core-auth
**Proyecto:** landlord / core
**Prioridad:** high
**Estado:** done

## Qué se hizo
- Creados atributos `#[Middleware]` y `#[Where]` en `app/Attributes/`
- Implementado `EndpointProcessor::getClassMiddleware()` — estaba vacío, causaba que el middleware de clase nunca se aplicara
- `BaseAuthController` abstracto en `app/Common/Http/Controller/Auth/` con template method pattern
- Guard `landlord` en `config/auth.php` (session driver, provider `landlord_users`)
- Middleware `EnsureAuthenticated` registrado como `auth.landlord` y `auth.tenant` en `bootstrap/app.php`
- `AuthController` de Landlord y ActivitiesBoard extendiendo `BaseAuthController`
- `AdminController` protegido con `#[Middleware(['auth.landlord'])]`

## Archivos clave
- `app/Attributes/Middleware.php`
- `app/Common/Http/Controller/Auth/BaseAuthController.php`
- `app/Http/Middleware/EnsureAuthenticated.php`
- `config/auth.php` → guard `landlord`
