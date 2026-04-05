# 01 — Migración campo `locale` en users y tenants

**Track:** language-config
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Agregar columna `locale VARCHAR(10) NULL` en tabla `users` (central y tenant) y `locale VARCHAR(10) DEFAULT 'es'` en tabla `tenants`.
