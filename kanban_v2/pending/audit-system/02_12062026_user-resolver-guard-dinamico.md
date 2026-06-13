# 02 — UserResolver con guard dinámico

**Track:** audit-system
**Proyecto:** core
**Prioridad:** high
**Estado:** pending

## Qué hacer
Crear `app/Common/Audit/UserResolver.php` implementando `OwenIt\Auditing\Contracts\UserResolver`.

```php
class UserResolver implements \OwenIt\Auditing\Contracts\UserResolver
{
    public static function resolve(): ?Authenticatable
    {
        if (tenancy()->initialized) {
            return auth('web')->user();
        }
        return auth('landlord')->user();
    }
}
```

Registrar en `config/audit.php`:
```php
'user' => [
    'resolver' => \App\Common\Audit\UserResolver::class,
],
```

## Criterio de aceptación
- En contexto tenant: auditoría registra el usuario autenticado con guard `web`
- En contexto landlord: registra el usuario con guard `landlord`
- No lanza error si no hay usuario autenticado (retorna null)
