# PR — Impersonación de usuarios dentro del tenant (#020-#022)

**Track:** user-impersonation
**Proyecto:** core + activities-board
**Prioridad:** high
**Estado:** in-progress
**Security Agent:** ACTIVO — middleware de auth, impersonation con guard web, session manipulation, bootstrap/app.php

## Descripción

El system_user ya está logueado en el tenant (via #019). Este PR le permite seleccionar un usuario real del tenant y suplantarlo, con banner visible y opción de salir. Resuelve también la deuda del PR anterior: `canBeImpersonated()` que estaba implementado pero nunca invocado.

## Sub-features

- SF-01: Middleware `EnsureIsSystemUser` + alias en bootstrap/app.php — bajo — sin dependencias
- SF-02: `TenantImpersonationController` en ActivitiesBoard (start/stop), Routes enum, config/projects.php — medio — depende SF-01
- SF-03: Soporte `condition` callable en `ListAction` + acción "Suplantar" en `ActivitiesBoard\UserAdmin` — bajo — paralelo con SF-01
- SF-04: Banner en `layout_menu_sidebar.blade.php` para `session('system_impersonator_id')` — bajo — depende SF-02

## Decisiones arquitectónicas

**Session key: `system_impersonator_id` (no `impersonator_id`)**
El layout ya usa `impersonator_id` para la suplantación del landlord. Usar una key distinta evita colisiones y permite diferenciar los dos tipos de banner.

**Middleware `EnsureIsSystemUser` en lugar de permiso Spatie**
La condición `is_system_user=true` es un atributo del modelo, no un permiso de rol. Un middleware es más explícito y directo. Se registra con alias `auth.system_user` en bootstrap/app.php.

**`condition` callable en `ListAction`**
La acción "Suplantar" solo debe verse cuando el usuario logueado es system_user. Se agrega `$options['condition']` como closure `fn($currentUser) => ...` evaluada en el blade. Alternativa descartada: permission de Spatie (superadmin ya tiene todos los permisos, no diferencia system_user).

**`canBeImpersonated()` finalmente invocado**
El controller verifica `$target->canBeImpersonated()` antes del login — salda la deuda del PR anterior.

**Stop flow devuelve al system_user, no al landlord**
`stopImpersonation()` restaura la sesión del system_user (via `session('system_impersonator_id')`). El botón "Salir del tenant" del banner es un link separado de vuelta al landlord (usar `config('app.url')` o una ruta dedicada).

## Archivos a modificar/crear

- `app/Http/Middleware/EnsureIsSystemUser.php` (nuevo) — SF-01
- `bootstrap/app.php` — SF-01
- `app/Projects/ActivitiesBoard/Http/Controller/Admin/ImpersonationController.php` (nuevo) — SF-02
- `app/Projects/ActivitiesBoard/Enums/Routes.php` — SF-02
- `config/projects.php` — SF-02
- `app/Common/Admin/models/ListView/ListAction.php` — SF-03
- `resources/views/landlord/list.blade.php` — SF-03 (ya sirve para tenant también via mismo layout)
- `app/Projects/ActivitiesBoard/Adapters/Admin/UserAdmin.php` — SF-03
- `resources/views/layouts/layout_menu_sidebar.blade.php` — SF-04

## Notas del dev_log

- Sesiones landlord y web son independientes por subdominio — sin riesgo cross-guard
- `canBeImpersonated()` estaba definido pero nunca invocado — este PR lo usa
- El `TenantSystemUserSeeder` creó el usuario base que actúa como impersonator
