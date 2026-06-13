# 01 — Setup Restify + Sanctum

**Track:** restify-api
**Proyecto:** core
**Prioridad:** high
**Estado:** pending

## Qué hacer
- `composer require binaryk/laravel-restify laravel/sanctum`
- Publicar configs: `php artisan vendor:publish --provider="...SanctumServiceProvider"`
- Crear migration `personal_access_tokens`
- Agregar `HasApiTokens` a `App\Models\User`
- Crear `routes/api.php` con grupo `{project}/api`
- Registrar en `bootstrap/app.php`

## Archivos clave
- `routes/api.php` (nuevo)
- `app/Models/User.php` → trait `HasApiTokens`
- `bootstrap/app.php` → registrar api routes
- `config/restify.php`, `config/sanctum.php`

## Criterio de aceptación
- `GET {tenant}.localhost/activities-board/api` retorna 200 JSON de Restify
