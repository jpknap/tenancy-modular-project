# 06 — UI: asignación de permisos extra al rol admin (fase 2)

**Track:** permission-system
**Proyecto:** core
**Prioridad:** low
**Estado:** pending

## Qué hacer
Vista solo para superadmin: `/admin/settings/permissions`
- Lista todos los permisos disponibles con checkboxes
- Muestra cuáles tiene asignados el rol `admin` actualmente
- POST sincroniza con `$adminRole->syncPermissions($selected)`

## Consideraciones
- Los permisos disponibles son los definidos en el seeder (no se crean permisos custom desde UI)
- Permisos reservados a superadmin no aparecen como opciones (e.g. `roles:assign`)
- Cambiar permisos del rol `admin` afecta a todos los admins del tenant

## Criterio de aceptación
- Superadmin puede activar `users:impersonate` para el rol admin
- Cambio se refleja inmediatamente (spatie limpia cache de permisos)
- Admins sin `users:impersonate` no ven el botón de suplantar en la lista de usuarios
