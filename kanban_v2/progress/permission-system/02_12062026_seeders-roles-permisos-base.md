# 02 — Seeders: roles y permisos base por tenant

**Track:** permission-system
**Proyecto:** core
**Prioridad:** high
**Estado:** in-progress

## Qué hacer
Crear `RolesAndPermissionsSeeder` que corre en cada nuevo tenant:

### Roles
- `superadmin` — acceso total (via Gate::before, no permisos asignados directamente)
- `admin` — gestión del tenant, permisos configurables
- `user` — acceso básico al proyecto

### Permisos base
```
users:list
users:create
users:edit
users:delete
users:impersonate

roles:assign

settings:general
```

### Asignación inicial
- `admin` recibe: `users:list`, `users:create`, `users:edit`, `users:delete`, `settings:general`
- `admin` NO recibe por defecto: `users:impersonate`, `roles:assign` (los asigna el superadmin si decide dárselos)
- `user` no recibe permisos de gestión

### Hook en TenantService
Llamar `RolesAndPermissionsSeeder::run()` dentro del contexto del tenant en `TenantService::create()`, después de correr migraciones.

## Criterio de aceptación
- Crear un tenant nuevo resulta en roles y permisos creados en su schema
- Los permisos existen en la tabla `permissions` del tenant
