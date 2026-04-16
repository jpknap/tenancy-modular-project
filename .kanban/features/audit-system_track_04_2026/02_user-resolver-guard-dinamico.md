# 02 — UserResolver con guard dinámico

**Track:** audit-system
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Crear `app/Common/Audit/UserResolver.php` implementando `OwenIt\Auditing\Contracts\UserResolver`. Detecta `tenancy()->initialized` para usar guard `web` (tenant) o `landlord` (central). Registrar en `config/audit.php`.
