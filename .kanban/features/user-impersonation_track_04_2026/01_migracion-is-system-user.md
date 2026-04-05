# 01 — Migración `is_system_user` en users del tenant

**Track:** user-impersonation
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Migración de tenant: `ALTER TABLE users ADD is_system_user BOOLEAN DEFAULT FALSE`. Scope en modelo User del tenant para excluirlo de todos los queries normales.
