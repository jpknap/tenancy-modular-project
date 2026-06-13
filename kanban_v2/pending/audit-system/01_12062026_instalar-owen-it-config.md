# 01 — Instalar owen-it/laravel-auditing y configuración base

**Track:** audit-system
**Proyecto:** core
**Prioridad:** high
**Estado:** pending

## Qué hacer
- `composer require owen-it/laravel-auditing:^13.0`
- `php artisan vendor:publish --provider="OwenIt\Auditing\AuditingServiceProvider" --tag="config"`
- Configurar `config/audit.php`:
  - `'connection' => null` — hereda el schema activo de stancl/tenancy
  - `'threshold' => 0`
  - `'user_resolver'` apuntar al `UserResolver` personalizado (SF-02)

## Criterio de aceptación
- Paquete instalado sin conflictos
- `config/audit.php` publicado y configurado
- El resolver por defecto reemplazado por el custom
