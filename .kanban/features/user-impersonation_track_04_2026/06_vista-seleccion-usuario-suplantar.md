# 06 — Vista de selección de usuario para suplantar

**Track:** user-impersonation
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

`SystemImpersonationController` protegido por `EnsureIsSystemUser`. Lista usuarios del tenant (excluyendo system_user). Botón "Suplantar" por fila → POST `/system/impersonate/{userId}`.
