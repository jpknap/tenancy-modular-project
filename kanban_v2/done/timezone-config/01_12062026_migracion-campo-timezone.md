# 01 — Campo `timezone` en migraciones existentes

**Track:** timezone-config
**Proyecto:** core
**Prioridad:** high
**Estado:** done

## Qué se hizo
Campos agregados directamente en las migraciones existentes (proyecto nuevo, sin datos que preservar):
- `database/migrations/0001_01_01_000000_create_users_table.php` → `timezone VARCHAR(50) NULL`
- `database/migrations/projects/Common/0001_01_01_000000_create_users_table.php` → `timezone VARCHAR(50) NULL`
- `database/migrations/2019_09_15_000010_create_tenants_table.php` → `timezone VARCHAR(50) DEFAULT 'UTC'`
