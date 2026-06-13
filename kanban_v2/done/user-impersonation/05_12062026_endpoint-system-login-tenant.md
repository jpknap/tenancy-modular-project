# 05 — Endpoint /system-access/{token} en Tenant

**Track:** user-impersonation
**Proyecto:** core
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `SystemLoginController` en `app/Common/Http/Controller/`
- Ruta manual en `routes/tenant.php`: `GET /system-access/{token}`
- Validaciones: HMAC válido + no expirado + one-time-use + `is_system_user=true`
- Usa cross-connection explícita a BD central para validar el token
- `Auth::loginUsingId($systemUser->id)` tras validación exitosa
