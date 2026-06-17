# Análisis: Gestión dinámica de roles y permisos con Spatie

**Fecha:** 2026-06-12  
**Track:** permission-system  
**Contexto:** El proyecto ya usa `spatie/laravel-permission ^8.0`. Roles (`superadmin`, `admin`, `user`) y permisos están hardcodeados en `UserRole` enum + `RolesAndPermissionsSeeder`. La feature 05 asigna roles desde UI, pero el set de roles/permisos no es configurable por el administrador del tenant.

---

## Problema que resuelve

Actualmente el superadmin puede *asignar* un rol a un usuario, pero no puede *crear* un rol nuevo ni *modificar* qué permisos tiene. Si se necesita un rol "Coordinador" con acceso parcial, hay que tocar código PHP y hacer deploy.

El objetivo es: **el superadmin puede crear roles, asignarles permisos de un catálogo definido en código, y asignar esos roles a usuarios — sin tocar código.**

---

## Premisas de diseño

| Premisa | Razón |
|---|---|
| Los **permisos** siguen definidos en código/config | Evitar permission sprawl; los permisos representan capacidades del sistema que solo cambian con código |
| Los **roles** son dinámicos (creados en DB por admin) | Permiten agrupaciones de negocio sin deploy |
| Multi-tenant: cada tenant tiene su propio set de roles/permisos | Ya garantizado por Spatie con `teams` o schema separado (Tenancy for Laravel) |
| Los tres roles base (`superadmin`, `admin`, `user`) siguen existiendo | Son roles estructurales del sistema, no de negocio |

---

## Catálogo de permisos base (registrado en código)

El catálogo se mueve de un seeder hardcodeado a una fuente canónica. Dos enfoques posibles:

```php
// config/permissions.php
return [
    'users' => [
        'users:list'        => 'Listar usuarios',
        'users:create'      => 'Crear usuarios',
        'users:edit'        => 'Editar usuarios',
        'users:delete'      => 'Eliminar usuarios',
        'users:impersonate' => 'Impersonar usuarios',
    ],
    'roles' => [
        'roles:assign'      => 'Asignar roles',
        'roles:manage'      => 'Gestionar roles (nuevo)',
    ],
    'settings' => [
        'settings:general'  => 'Configuración general',
    ],
];
```

El seeder lee este archivo → los permisos se crean/sincronizan al migrar el tenant. **Añadir un permiso nuevo = editar config, correr seeder.** El admin del tenant no puede inventar permisos nuevos, solo combinarlos en roles.

---

## Matriz de evaluación

| Criterio | Peso | Opción A — Roles dinámicos, permisos fijos | Opción B — Roles y permisos completamente dinámicos |
|---|:---:|---|---|
| **Simplicidad de implementación** | 20% | ★★★★★ Config + UI CRUD de roles | ★★★ UI doble: permisos + roles |
| **Control y auditabilidad** | 25% | ★★★★★ Permisos en código, versionados con git | ★★★ Permisos en DB, drift posible |
| **Flexibilidad para el tenant** | 20% | ★★★★ Puede crear cualquier rol combinando permisos existentes | ★★★★★ Puede crear permisos propios también |
| **Riesgo de errores del admin** | 20% | ★★★★★ Solo puede mezclar permisos válidos | ★★★ Puede crear permisos huérfanos |
| **Mantenibilidad a largo plazo** | 15% | ★★★★★ Permisos rastreables en code review | ★★★ Documentar convenciones es crítico |
| **Tiempo de implementación** | — | ~2-3 días | ~4-5 días |

**Puntaje ponderado:**  
- Opción A: **4.7 / 5**  
- Opción B: **3.8 / 5**

---

## Opción A — Roles dinámicos, permisos fijos en config

### Concepto
El superadmin ve todos los permisos disponibles (leídos de `config/permissions.php`) y puede:
1. Crear un nuevo rol con nombre libre
2. Marcar qué permisos tiene ese rol (checkboxes)
3. Asignar ese rol a cualquier usuario

Los permisos no se pueden crear desde UI — solo desde código + seeder.

### Flujo

```
config/permissions.php (fuente canónica)
        ↓ seeder al crear tenant
permissions table (DB del tenant)
        ↓ UI: RoleController (nuevo)
roles table + role_has_permissions (DB del tenant)
        ↓ UI: UserController (existente, feature 05)
model_has_roles (DB del tenant)
```

### Componentes a crear

```
app/
├── Http/Controller/Admin/RoleController.php   # CRUD de roles
├── FormRequests/RoleFormRequest.php           # validación
├── Services/Model/RoleService.php             # lógica
config/
└── permissions.php                            # catálogo canónico
resources/views/admin/roles/
├── index.blade.php
├── create.blade.php
└── edit.blade.php                             # checkboxes de permisos
```

### Ventajas
- Minimal: reutiliza todo lo que Spatie ya tiene
- Permisos en git → auditoría completa
- El admin no puede romper el sistema inventando permisos con typos
- El `RolesAndPermissionsSeeder` solo sincroniza desde config (idempotente)

### Restricciones
- Roles `superadmin`, `admin`, `user` son inmutables desde UI (protección en service)
- No se pueden eliminar permisos del catálogo si están en uso (validación en seeder)
- `superadmin` bypasea todo con `Gate::before` (no cambia)

### Snippet clave

```php
// RoleService.php
public function createRole(string $name, array $permissionNames): Role
{
    $reserved = ['superadmin', 'admin', 'user'];
    throw_if(in_array($name, $reserved), \InvalidArgumentException::class, 'Rol reservado');

    $role = Role::create(['name' => $name, 'guard_name' => 'web']);
    $role->syncPermissions($permissionNames);
    return $role;
}
```

---

## Opción B — Roles y permisos completamente dinámicos

### Concepto
Tanto roles como permisos se gestionan desde UI. El superadmin puede:
1. Crear permisos con nombre libre (ej: `reportes:exportar`)
2. Crear roles
3. Asignar permisos a roles
4. Asignar roles a usuarios

El código usa los permisos por string (`$user->can('reportes:exportar')`) — si el permiso no existe en DB, `can()` devuelve `false` silenciosamente.

### Componentes adicionales vs Opción A

```
app/
├── Http/Controller/Admin/PermissionController.php  # CRUD de permisos (nuevo)
├── FormRequests/PermissionFormRequest.php
├── Services/Model/PermissionService.php
resources/views/admin/permissions/
├── index.blade.php
└── create.blade.php
```

### Ventajas
- Máxima flexibilidad: el tenant puede modelar su negocio
- Extensible para futuros módulos sin tocar código

### Desventajas críticas
- Los `@can('permiso')` en Blade y policies deben coincidir exactamente con lo que el admin escribió → errores silenciosos
- No hay forma de saber desde código qué permisos existen y cuáles son realmente usados
- Migration entre tenants difícil: permisos distintos por tenant
- Requiere documentación de convenciones fuerte para evitar `usuarios:listar` vs `users:list` en el mismo sistema

### Cuándo tiene sentido
- El tenant tiene módulos propios con acciones muy específicas de negocio
- Hay un equipo técnico dedicado en cada tenant que mantiene las convenciones
- Se necesita extensibilidad real (plugins, módulos opcionales)

---

## Recomendación

**Implementar Opción A.**

El 95% del valor (el admin puede crear roles a medida) se obtiene con el 60% del esfuerzo. Los permisos representan capacidades del sistema — si cambia una capacidad, cambia el código, y tiene sentido que el permiso también esté en el repositorio. La Opción B agrega complejidad accidental sin resolver un problema real en este proyecto.

### Roadmap sugerido

| Paso | Feature | Estado actual |
|---|---|---|
| 1 | Mover permisos a `config/permissions.php` + refactor seeder | Pendiente |
| 2 | CRUD de roles en admin UI (`RoleController`) | Pendiente |
| 3 | Asignación de permisos a roles (checkboxes en edit) | Pendiente |
| 4 | Asignación de rol a usuario (feature 05, en curso) | In-progress |
| 5 | Columna "Rol" en lista de usuarios + permisos adicionales (feature 05) | In-progress |

Los pasos 1-3 son prerequisito lógico de la feature 05 para que el select de roles no sea hardcodeado.

---

## Consideraciones multi-tenant

`stancl/tenancy` con schemas separados hace que cada tenant tenga sus propias tablas `roles`, `permissions`, `role_has_permissions`. Spatie lo soporta nativamente en este modo — no requiere configuración extra de `teams`. El seeder corre dentro del contexto del tenant (`$tenant->run(fn() => ...)`) y crea los roles/permisos en el schema correcto.

Al crear roles dinámicos desde UI, el tenant solo ve y modifica sus propios roles — el aislamiento ya está dado por la conexión de DB activa.
