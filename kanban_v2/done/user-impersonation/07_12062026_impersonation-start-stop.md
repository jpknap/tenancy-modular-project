# 07 — ImpersonationService (start/stop)

**Track:** user-impersonation
**Proyecto:** core
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `ImpersonationController` → `start(User $target)`: valida que no sea system_user, `Auth::login($target)`, guarda `session(['system_impersonator_id' => $systemUser->id])`
- `StopImpersonationController` → `stop()`: `Auth::login($systemUser)`, limpia sesión de suplantación
- Sesión guarda: `system_entry`, `landlord_admin_id`, `impersonating_user_id`
