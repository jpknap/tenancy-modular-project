# 01 — F1: Setup Restify + Sanctum

**Track:** restify-api
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Instalar `binaryk/laravel-restify` y `laravel/sanctum`. Publicar configs, crear migración `personal_access_tokens`, agregar `HasApiTokens` a `App\Models\User`, crear `routes/api.php` con grupo `{project}/api` y registrarlo en `bootstrap/app.php`.

**Archivos clave:**
- `routes/api.php` (nuevo)
- `app/Models/User.php` → trait `HasApiTokens`
- `bootstrap/app.php` → registrar api routes
- `config/restify.php`, `config/sanctum.php`

**Criterio de aceptación:** `GET {tenant}.host.cl/activities-board/api` retorna 200 JSON de Restify.
