# 04 — Middleware CheckPermission en rutas admin

**Track:** permission-system
**Proyecto:** core
**Prioridad:** medium
**Estado:** in-progress

## Qué hacer
Spatie registra automáticamente el middleware `permission` y `role`. Registrar los aliases en `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
})
```

### Uso en controladores (via atributo #[Middleware])
```php
#[Middleware(['auth.tenant', 'permission:users:impersonate'])]
public function impersonate(int $userId): RedirectResponse { ... }
```

### Alternativa con Gate en el controlador
```php
$this->authorize('users:impersonate'); // lanza 403 si no tiene permiso
```

## Criterio de aceptación
- Ruta protegida con `permission:users:impersonate` retorna 403 para usuario sin ese permiso
- Superadmin pasa el check sin tener el permiso asignado explícitamente (Gate::before)
