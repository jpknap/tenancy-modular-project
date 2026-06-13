# 06 — Vista de selección de usuario para suplantar

**Track:** user-impersonation
**Proyecto:** core
**Prioridad:** high
**Estado:** done

## Qué se hizo
- Acción "Suplantar" en `UserAdmin` con condition callable — solo visible para `system_user`
- Middleware `EnsureIsSystemUser` protege el controlador: cualquier otra ruta redirige a la selección
- Lista de usuarios excluye `is_system_user=true`
