# 01d — Invalidación de cache de roles vía evento Spatie (RoleAttachedEvent / RoleDetachedEvent)

**Track:** permission-system
**Proyecto:** core
**Prioridad:** high
**Estado:** done ✅
**Depende de:** 01c (CacheUserRoles middleware + cache por usuario) ✅

---

## Por qué se refactoriza lo hecho en 01c

En 01c cada `UserService` llama explícitamente a `forgetUserRolesCache()` después de `syncRoles()`:

```php
// ActivitiesBoard, SportCompetition — duplicado en cada servicio
$user->syncRoles([$role]);
$this->forgetUserRolesCache($user);   // ← acoplamiento manual
```

**Problemas de ese enfoque:**

| Problema | Consecuencia |
|---|---|
| Cada servicio debe *recordar* llamar al método | Si se agrega un proyecto nuevo o alguien llama `syncRoles()` desde un controller, el cache queda sucio |
| La lógica de cache está acoplada a la capa de servicio | El `UserService` sabe demasiado: persiste datos Y gestiona cache |
| No cubre `assignRole()` / `removeRole()` directos | Si en algún momento se asignan roles fuera del flujo de `UserService`, no hay invalidación |

---

## La solución: escuchar los eventos nativos de Spatie

Spatie dispara eventos propios cada vez que se modifica la asignación de rol de cualquier modelo:

```
$user->syncRoles(['admin'])
  └─ removeRole(rolesActuales)
       └─ roles()->detach()
       └─ event(new RoleDetachedEvent($user, $roles))   ← evento nativo Spatie
  └─ assignRole(['admin'])
       └─ roles()->attach()
       └─ event(new RoleAttachedEvent($user, $roles))   ← evento nativo Spatie
```

Firma del evento (igual para Attached y Detached):
```php
// vendor/spatie/laravel-permission/src/Events/RoleAttachedEvent.php
public function __construct(public Model $model, public mixed $rolesOrIds) {}
//                                  ^^^^^ el usuario concreto cuyo rol cambió
```

El listener resultante:

```php
class InvalidateUserRolesCache
{
    public function handle(RoleAttachedEvent|RoleDetachedEvent $event): void
    {
        if (! tenancy()->initialized) {
            return;   // contexto Landlord: no hay cache de tenant para invalidar
        }

        $tenantKey = tenancy()->tenant->getTenantKey();
        cache()->forget("user.{$event->model->id}.roles.tenant.{$tenantKey}");
    }
}
```

---

## Por qué esta solución escala

### Invalidación quirúrgica — solo el usuario afectado

```
syncRoles() en user.id=42
  └─ RoleAttachedEvent($user42, ...)
       └─ cache()->forget("user.42.roles.tenant.acme")   ← solo ese usuario

  El resto de usuarios: sus entradas de cache no se tocan
  user.1.roles.tenant.acme  → intacto hasta TTL
  user.7.roles.tenant.acme  → intacto hasta TTL
```

Contraste con `forgetCachedPermissions()` (01b), que borra las *definiciones* de roles para todo el tenant (todos los usuarios se ven afectados). Este listener es quirúrgico: afecta exactamente el usuario cuyo rol cambió.

### Compatible con Landlord sin código extra

El Landlord opera en contexto central (`tenancy()->initialized === false`). El listener detecta esto en la primera línea y retorna sin hacer nada. Los usuarios del Landlord:
- No pasan por `CacheUserRoles` middleware (01c ya lo maneja)
- No tienen entrada en el cache store de tenant
- No necesitan invalidación

Cero código condicional en los `UserService`.

### Compatible con cualquier punto de asignación de roles

El evento se dispara desde dentro de Spatie, no desde el servicio. Si en el futuro un comando artisan, un seeder, un job o un controller llama `syncRoles()` / `assignRole()` / `removeRole()` directamente, la invalidación ocurre igual — sin que ese código sepa que existe un cache.

### Compatible con nuevos proyectos

Agregar un tercer tenant proyecto (`SportCompetition`, `ActivitiesBoard`, `FutureProject`) no requiere tocar el listener. El patrón de cache key `user.{id}.roles.tenant.{key}` es uniforme para todos.

---

## Por qué no había que hacer esto antes (requisito previo)

Los eventos `RoleAttachedEvent` / `RoleDetachedEvent` están **desactivados por defecto** en Spatie:

```php
// config/permission.php
'events_enabled' => false,   // ← hay que activarlo
```

Hasta que 01c no estableció el cache por usuario, activar estos eventos no tenía sentido (no había nada que invalidar). Ahora sí.

---

## Qué hacer

### SF-01: Activar eventos en config/permission.php
```php
'events_enabled' => true,
```

### SF-02: Crear App\Listeners\InvalidateUserRolesCache

```
app/Listeners/InvalidateUserRolesCache.php
```

- Escucha `RoleAttachedEvent` y `RoleDetachedEvent`
- Guard: `if (! tenancy()->initialized) return`
- Invalida `cache()->forget("user.{$event->model->id}.roles.tenant.{tenantKey}")`

### SF-03: Registrar el listener en AppServiceProvider (o TenancyServiceProvider)

```php
Event::listen(
    [RoleAttachedEvent::class, RoleDetachedEvent::class],
    InvalidateUserRolesCache::class
);
```

### SF-04: Limpiar los tres UserService

Eliminar `forgetUserRolesCache()` privado de `ActivitiesBoard\UserService` y `SportCompetition\UserService`. Los servicios quedan con `syncRoles()` únicamente — sin saber nada del cache.

### SF-05: Test del listener

`tests/Unit/Permission/InvalidateUserRolesCacheTest.php`

- Verifica que en contexto tenant invalida la key correcta
- Verifica que en contexto Landlord (no inicializado) no toca el cache
- Verifica que dos usuarios distintos tienen keys distintas (no hay contaminación cruzada)

---

## Flujo completo resultante (01b + 01c + 01d)

```
Request llega al tenant
  └─ TenancyInitialized → FlushPermissionCache (01b)
       └─ cacheKey = "spatie.permission.cache.tenant.{id}"

  └─ CacheUserRoles middleware (01c)
       └─ cache()->remember("user.{uid}.roles.tenant.{tid}", 10min, fn → DB)
       └─ $user->setRelation('roles', $cached)
            └─ hasRole() → usa la colección en memoria, 0 queries

Admin cambia rol de user.42
  └─ UserService::update() → $user->syncRoles(['admin'])
       └─ RoleDetachedEvent → InvalidateUserRolesCache (01d)
            └─ cache()->forget("user.42.roles.tenant.acme")
       └─ RoleAttachedEvent → InvalidateUserRolesCache (01d)
            └─ cache()->forget("user.42.roles.tenant.acme")   (idempotente)

Próximo request de user.42
  └─ CacheUserRoles: cache miss → recarga desde DB → recachea con rol nuevo ✓
```

---

## Criterio de aceptación

- [ ] `config/permission.php` tiene `events_enabled = true`
- [ ] `InvalidateUserRolesCache` listener existe y está registrado
- [ ] `forgetUserRolesCache()` eliminado de los tres `UserService`
- [ ] Los servicios solo llaman `syncRoles()`, sin lógica de cache
- [ ] Tests del listener pasan
- [ ] `php artisan test` sin regresiones

## Archivos a crear/modificar

| Archivo | Cambio |
|---|---|
| `config/permission.php` | `events_enabled => true` |
| `app/Listeners/InvalidateUserRolesCache.php` | nuevo |
| `app/Providers/AppServiceProvider.php` (o TenancyServiceProvider) | registrar listener |
| `app/Projects/ActivitiesBoard/Services/Model/UserService.php` | eliminar `forgetUserRolesCache()` |
| `app/Projects/SportCompetition/Services/Model/UserService.php` | eliminar `forgetUserRolesCache()` |
| `tests/Unit/Permission/InvalidateUserRolesCacheTest.php` | nuevo |
