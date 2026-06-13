# 01 — Migración campo `locale` en users y tenants

**Track:** language-config
**Proyecto:** core
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `locale VARCHAR(10) NULL` en tabla `users` central y en schema tenant
- `locale VARCHAR(10) DEFAULT 'es'` en tabla `tenants`
