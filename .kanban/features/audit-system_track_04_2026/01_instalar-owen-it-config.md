# 01 — Instalar owen-it/laravel-auditing y configuración base

**Track:** audit-system
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Instalar `owen-it/laravel-auditing:^13.0`, publicar `config/audit.php`. Configurar `connection = null` (hereda schema activo de stancl/tenancy), `threshold = 0`, y registrar el `UserResolver` personalizado.
