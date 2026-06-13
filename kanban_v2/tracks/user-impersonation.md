# Track — Suplantación de Usuario (user-impersonation)

## Estado actual
**Completado** — flujo completo funcional. Solo queda SF-10 (log de auditoría).

## Descripción
Un administrador Landlord puede suplantar a cualquier usuario de un tenant. El mecanismo usa un `system_user` oculto pre-creado en cada tenant como puente de entrada cross-domain.

---

## Flujo completo

```
[Landlord] Admin click "Acceder al Tenant"
    → Genera token HMAC-SHA256 (TTL 60s, one-time-use, guardado hasheado en Cache)
    → Redirige a tenant.localhost/system-access/{token}

[Tenant] Endpoint /system-access/{token}
    → Valida token contra BD central (cross-connection explícita)
    → Auth::loginUsingId($systemUser->id)
    → Token marcado como usado en Cache

[Tenant] Como system_user
    → Solo ve pantalla de selección de usuario (middleware EnsureIsSystemUser)
    → Click "Suplantar" en usuario objetivo

[Tenant] ImpersonationService::start($target)
    → Auth::login($target) — mismo guard, mismo schema
    → session(['system_impersonator_id' => $systemUser->id])
    → Banner rojo aparece en layout

[Tenant] Salir de suplantación
    → ImpersonationService::stop() → Auth::login($systemUser)
    → O "Salir del tenant" → invalida sesión, redirect a admin.localhost
```

---

## Componentes clave

| Componente | Archivo |
|-----------|---------|
| Genera token + botón | `TenantAccessController.php` (Landlord) |
| Consume token | `SystemLoginController.php` (Common) |
| Start/Stop impersonation | `ImpersonationController.php` (Common) |
| Selección de usuario | `SystemImpersonationController.php` |
| Banner en layout | `resources/views/layouts/layout_menu_sidebar.blade.php` |
| Protección system_user | Middleware `EnsureIsSystemUser` |

---

## Sub-features

| # | Feature | Estado |
|---|---------|--------|
| #015–#022 | SF-01 a SF-09 | ✅ done — ver `done/user-impersonation.md` |
| #023 | Log de auditoría `impersonation_logs` | ⏳ pending — ver `pending/user-impersonation/` |
