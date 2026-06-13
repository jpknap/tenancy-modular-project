# 03 — HasRoles en User models + Gate::before para superadmin

**Track:** permission-system
**Proyecto:** core
**Prioridad:** high
**Estado:** in-progress

## Qué hacer

### User model del tenant
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    public function canImpersonate(): bool
    {
        if ($this->is_system_user) return true;
        return $this->hasRole('superadmin')
            || $this->hasPermissionTo('users:impersonate');
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->is_system_user && ! $this->hasRole('superadmin');
    }
}
```

### Gate::before en TenantServiceProvider
```php
Gate::before(function ($user, $ability) {
    if ($user->hasRole('superadmin')) return true;
    return null;
});
```

## Criterio de aceptación
- `$user->can('users:impersonate')` retorna true para superadmin sin asignarle el permiso
- `$user->can('users:impersonate')` retorna true para admin con ese permiso asignado
- `$user->can('users:impersonate')` retorna false para admin sin ese permiso
- `canImpersonate()` retorna true para system_user independientemente de roles
