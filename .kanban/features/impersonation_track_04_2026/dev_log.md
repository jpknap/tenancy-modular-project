# Dev Log — Track: Impersonation (Suplantación de usuarios)

## Resumen
Track implementado en rama `feat/permission-system` junto al sistema de permisos.

## Features completadas
- [x] 01 — `permission` y `form_method` en `ListAction` + `list.blade.php`
- [x] 02 — `ImpersonationController` en SportCompetition y Landlord
- [x] 03 — Botón "Suplantar" en `UserAdmin` de ambos proyectos
- [x] 04 — Banner de modo suplantación en layout

## Archivos modificados
- `app/Common/Admin/models/ListView/ListAction.php`
- `resources/views/landlord/list.blade.php`
- `resources/views/layouts/layout_menu_sidebar.blade.php`
- `app/Projects/SportCompetition/Http/Controller/Admin/ImpersonationController.php` (nuevo)
- `app/Projects/Landlord/Http/Controller/Admin/ImpersonationController.php` (nuevo)
- `app/Projects/SportCompetition/Adapters/Admin/UserAdmin.php`
- `app/Projects/Landlord/Adapters/Admin/UserAdmin.php`
- `app/Projects/SportCompetition/Enums/Routes.php`
- `app/Projects/Landlord/Enums/Routes.php`
- `config/projects.php`
