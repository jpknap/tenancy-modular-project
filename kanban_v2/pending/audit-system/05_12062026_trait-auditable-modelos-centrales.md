# 05 — Trait Auditable en modelos centrales (User, Tenant)

**Track:** audit-system
**Proyecto:** core
**Prioridad:** high
**Estado:** pending

## Qué hacer
Agregar `Auditable` trait a los modelos de la BD central:
- `app/Models/User.php`
- `app/Models/Tenant.php`

```php
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class User extends Authenticatable implements Auditable
{
    use AuditableTrait;

    protected array $auditExclude = ['password', 'remember_token'];
}
```

Sin `$auditConnection` en ninguno — en contexto landlord la conexión activa ya es la correcta.

## Criterio de aceptación
- Crear/editar/eliminar un `Tenant` genera registro en `audits` de la BD central
- Crear/editar un `User` (landlord) genera registro en `audits`
- El campo `password` nunca aparece en los audits
