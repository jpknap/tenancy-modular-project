# 01 — Migración `is_system_user` en users del tenant

**Track:** user-impersonation
**Proyecto:** core
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `is_system_user BOOLEAN DEFAULT FALSE` agregado directamente en la migración de creación de `users` del tenant
- Scope `scopeExcludeSystem()` en el modelo User del tenant para excluirlo de todos los queries normales
- Campo en `$fillable` del modelo User
