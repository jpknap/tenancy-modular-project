# 07 — ImpersonationService (start/stop)

**Track:** user-impersonation
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

`start(User $target)`: valida no es system_user, Auth::login($target), guarda en sesión. `stop()`: Auth::login($systemUser), limpia sesión. Registra en `impersonation_logs`.
