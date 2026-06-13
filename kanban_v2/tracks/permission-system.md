# Track — Sistema de Permisos (permission-system)

## Estado actual
**En progreso** — SF-01 a SF-05 activos, SF-06 pendiente.

## Descripción
Implementar un sistema de roles y permisos por tenant usando `spatie/laravel-permission`. Cada tenant tiene sus propios roles (`superadmin`, `admin`, `user`) y permisos granulares por entidad+acción (`users:impersonate`, `users:delete`, etc.). El superadmin tiene todos los permisos implícitamente via `Gate::before`.

---

## Arquitectura

### Librería: `spatie/laravel-permission`
- Integrada con Laravel Gate: `$user->can('users:impersonate')` funciona directamente
- Con `stancl/tenancy` (schemas separados) **no se necesita el feature teams** — cada schema ya aísla roles/permisos. Configurar `'teams' => false`.

### Convención de permisos — formato `{entidad}:{acción}`
```
users:list    users:create    users:edit    users:delete    users:impersonate
roles:assign
settings:general
```

### Roles base por tenant
| Rol | Descripción |
|-----|-------------|
| `superadmin` | Acceso total implícito via Gate::before. No editable. |
| `admin` | Acceso a gestión. Permisos configurables por superadmin. |
| `user` | Acceso básico. Sin permisos de gestión. |

### Multi-tenant: aislamiento de roles
Cada tenant tiene su propia tabla `roles`/`permissions` en su schema — el aislamiento ya está garantizado por `stancl/tenancy`. Sin teams de Spatie.

### Cache de permisos
Spatie cachea permisos. Con schemas separados la cache puede contaminar entre tenants si la key es la misma. Configurar `cache.key` dinámico o limpiar manualmente con `app(PermissionRegistrar::class)->forgetCachedPermissions()` al cambiar de tenant.

### Integración con impersonation
```php
public function canImpersonate(): bool
{
    if ($this->is_system_user) return true;
    return $this->hasRole('superadmin') || $this->hasPermissionTo('users:impersonate');
}

public function canBeImpersonated(): bool
{
    return ! $this->is_system_user && ! $this->hasRole('superadmin');
}
```

---

## Sub-features

| # | Feature | Archivo | Estado |
|---|---------|---------|--------|
| SF-01 | Instalar spatie + migraciones | `progress/permission-system/01_*` | 🔄 in-progress |
| SF-02 | Seeders roles/permisos base | `progress/permission-system/02_*` | 🔄 in-progress |
| SF-03 | HasRoles en User + Gate::before | `progress/permission-system/03_*` | 🔄 in-progress |
| SF-04 | Middleware CheckPermission | `progress/permission-system/04_*` | 🔄 in-progress |
| SF-05 | UI asignación de roles | `progress/permission-system/05_*` | 🔄 in-progress |
| SF-06 | UI permisos extra admin (fase 2) | `pending/permission-system/06_*` | ⏳ pending |

## MVP recomendado
SF-01 → SF-02 → SF-03 → SF-04 → SF-05. SF-06 queda para segunda iteración.

## Dependencia crítica
SF-03 debe completarse **antes** de avanzar en `restify-api` (F10), ya que `canImpersonate()` depende de `HasRoles`.
