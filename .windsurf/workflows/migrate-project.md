---
description: Ejecutar migraciones por proyecto en tenants existentes
---

# Workflow: Migraciones por Proyecto

Cuando agregas nuevas migraciones en `database/migrations/projects/Common/` o en algún proyecto específico como `SportCompetition/`, usa este comando para ejecutarlas en tenants existentes.

## Comandos Principales

### 1. Migrar todos los tenants
```bash
php artisan tenants:migrate-project
```

### 2. Migrar tenants específicos
```bash
php artisan tenants:migrate-project --tenant=1 --tenant=2
```

### 3. Migrar solo tenants de un proyecto
```bash
php artisan tenants:migrate-project --project=sport-competition
```

### 4. Migrar solo Common (sin proyecto específico)
```bash
php artisan tenants:migrate-project --common
```

### 5. Fresh + Seed (recrear todo)
⚠️ **BORRA TODOS LOS DATOS**
```bash
php artisan tenants:migrate-project --tenant=ID --fresh --seed
```

## Ejemplo Práctico

Agregaste una nueva tabla en `SportCompetition`:

**Paso 1:** Crear migración
```bash
# database/migrations/projects/SportCompetition/2024_12_01_create_nueva_tabla.php
```

**Paso 2:** Ejecutar en tenants de ese proyecto
```bash
php artisan tenants:migrate-project --project=sport-competition
```

**Resultado:** La nueva tabla se crea en todos los tenants con proyecto "sport-competition"

## Opciones del Comando

- `--tenant=ID` : Especificar tenant(s) por ID (múltiples permitidos)
- `--project=PREFIX` : Solo tenants de un proyecto
- `--common` : Solo migraciones Common
- `--fresh` : Drop todas las tablas y recrear
- `--seed` : Ejecutar seeders después

## Notas Importantes

✅ Las migraciones nuevas se ejecutan automáticamente al **crear** un tenant
📝 Este comando es para tenants **ya existentes**
🔍 Revisa los logs en `storage/logs/laravel.log`
