# Track 04 — Sistema de Permisos (Roles + Permissions)

## Descripción
Implementar un sistema de roles y permisos por tenant usando `spatie/laravel-permission`. Cada tenant tiene sus propios roles (superadmin, admin, user) y permisos granulares por entidad+acción (e.g. `users:impersonate`, `users:delete`). El superadmin tiene todos los permisos implícitamente. Los admins pueden tener permisos adicionales asignados manualmente.

---

## Análisis arquitectónico

### Librería: `spatie/laravel-permission`
- ~11k ⭐, estándar de facto en Laravel, pocos issues críticos abiertos
- Soporta **teams** (equivalente a tenants) para aislar roles/permisos por tenant
- Se integra con el modelo User via trait `HasRoles`
- Gate de Laravel funciona automáticamente: `$user->can('users:impersonate')`

### Convención de permisos
Formato: `{entidad}:{acción}` en minúsculas

```
users:list
users:create
users:edit
users:delete
users:impersonate

roles:assign

settings:general
```

### Roles base por tenant
| Rol | Descripción |
|-----|-------------|
| `superadmin` | Acceso total implícito. No se puede eliminar ni editar permisos. |
| `admin` | Acceso a gestión. Permisos configurables por superadmin. |
| `user` | Acceso limitado al proyecto. Sin permisos de gestión. |

### Multi-tenant: aislamiento de roles
`spatie/laravel-permission` usa `teams` para aislar roles por tenant. Con `stancl/tenancy` que usa schemas separados, **cada tenant ya tiene su propia tabla de roles/permisos** — no se necesita el feature de teams. Se deshabilita `teams` en el config.

### Integración con impersonation
```php
// User model (tenant)
public function canImpersonate(): bool
{
    // system_user siempre puede (bridge del landlord)
    if ($this->is_system_user) return true;

    return $this->hasRole('superadmin')
        || $this->hasPermissionTo('users:impersonate');
}

public function canBeImpersonated(): bool
{
    return ! $this->is_system_user && ! $this->hasRole('superadmin');
}
```

---

## Librerías

| Librería | Uso | Stars |
|----------|-----|-------|
| `spatie/laravel-permission` | Roles + permisos + Gate integration | ✅ ~11k ⭐ |

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

---

## Consideraciones críticas

1. **Schema por tenant** — con `stancl/tenancy`, las tablas `roles`, `permissions`, `model_has_roles` etc. se crean en cada schema de tenant via migración tenant. En el config de spatie: `'teams' => false`.

2. **Superadmin via Gate::before** — no asignar permisos uno a uno al superadmin. Usar:
   ```php
   // AppServiceProvider o TenantServiceProvider
   Gate::before(fn($user, $ability) => $user->hasRole('superadmin') ? true : null);
   ```

3. **Cache de permisos** — spatie cachea permisos. En tenants con schema separado, el cache puede contaminar entre tenants si se usa la misma key. Configurar `cache.key` dinámico o usar `PermissionRegistrar::setPermissionsTeamId()`.

4. **Seeder por tenant** — al crear un nuevo tenant, `TenantService::create()` debe correr un seeder que cree los roles base y el superadmin inicial.

5. **Proteger rutas admin** — el `EnsureAuthenticated` actual protege acceso. Agregar `CheckPermission` middleware para acciones específicas dentro del admin.

---

## Plan de implementación — Sub-features

### SF-01 · Instalar spatie/laravel-permission + migraciones
- `composer require spatie/laravel-permission`
- Publicar migraciones con `php artisan vendor:publish`
- Mover migraciones al path de migraciones tenant
- Configurar `config/permission.php`: `teams => false`, cache key por tenant

### SF-02 · Seeders: roles y permisos base por tenant
- `RolesAndPermissionsSeeder`: crea roles `superadmin`, `admin`, `user`
- Crea permisos: `users:list`, `users:create`, `users:edit`, `users:delete`, `users:impersonate`, `settings:general`
- Asigna todos los permisos al rol `admin` (excepto los reservados a superadmin)
- Hook en `TenantService::create()` para correr este seeder en cada tenant nuevo

### SF-03 · Trait HasRoles en User models + Gate::before
- Agregar `use HasRoles` en `User` model del tenant
- Agregar `Gate::before` en `TenantServiceProvider` para superadmin
- Implementar `canImpersonate()` y `canBeImpersonated()` (integración con lab404)

### SF-04 · Middleware `CheckPermission`
- Middleware reutilizable: `CheckPermission('users:impersonate')`
- Alternativa: usar `$this->middleware('can:users:impersonate')` en controladores (Gate nativo)
- Aplicar en rutas de admin que requieren permisos específicos

### SF-05 · UI: asignación de roles a usuarios
- En `UserAdmin::getListViewConfig()`: columna "Rol" + acción "Cambiar rol"
- Modal o redirect a formulario de edición con campo `role`
- Solo superadmin puede cambiar roles

### SF-06 · UI: asignación de permisos extra a rol admin (fase 2)
- Vista `/admin/roles` (solo superadmin)
- Lista de permisos disponibles con checkboxes por rol
- `syncPermissions()` de spatie

---

## Estimación de esfuerzo

| Sub-feature | Complejidad | Dependencias |
|------------|-------------|--------------|
| SF-01 Instalar + migraciones | Baja | Ninguna |
| SF-02 Seeders roles/permisos | Baja | SF-01 |
| SF-03 HasRoles + Gate::before | Baja | SF-01, SF-02 |
| SF-04 Middleware CheckPermission | Baja | SF-03 |
| SF-05 UI asignación roles | Media | SF-03 |
| SF-06 UI permisos por rol | Media | SF-05 |

**MVP recomendado:** SF-01 → SF-02 → SF-03 → SF-04 → SF-05
SF-06 queda para segunda iteración.

### Dependencia con impersonation
SF-03 debe completarse **antes** de implementar SF-07 del track `user-impersonation`, ya que `canImpersonate()` depende de `HasRoles`.
