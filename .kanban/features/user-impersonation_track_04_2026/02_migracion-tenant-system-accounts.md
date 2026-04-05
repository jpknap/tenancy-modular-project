# 02 — Migración `tenant_system_accounts` en BD central

**Track:** user-impersonation
**Proyecto:** landlord
**Prioridad:** high
**Estado:** backlog

Tabla central: `id, tenant_id, encrypted_password, access_token (hashed nullable), token_expires_at (nullable), timestamps`. Almacena credenciales encriptadas del system_user de cada tenant.
