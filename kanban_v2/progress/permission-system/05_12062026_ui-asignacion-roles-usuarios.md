# 05 — UI: asignación de roles a usuarios en admin

**Track:** permission-system
**Proyecto:** core
**Prioridad:** medium
**Estado:** in-progress

## Qué hacer
- En el formulario de edición de usuario (`UserFormRequest`): agregar campo `role` (select: superadmin/admin/user)
- Solo visible/editable para el superadmin (`@can('roles:assign')`)
- `UserService::update()`: sincronizar rol con `$user->syncRoles([$request->role])`
- En la lista de usuarios: columna "Rol" mostrando el rol actual

## Consideraciones
- Un usuario tiene **un solo rol** en este diseño (syncRoles reemplaza el anterior)
- El superadmin no puede cambiar su propio rol (evitar quedar sin superadmin)
- Mostrar permisos adicionales asignados al admin como lista informativa (edición en SF-06)

## Criterio de aceptación
- Superadmin puede cambiar el rol de cualquier usuario excepto el suyo propio
- Admin no ve el campo de rol en el formulario
- La columna "Rol" aparece en la lista de usuarios del admin
