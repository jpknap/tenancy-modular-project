# Migraciones por Proyecto en Tenants

## Descripción General

El sistema permite ejecutar migraciones específicas según el proyecto asignado a cada tenant. Esto se maneja automáticamente al crear un tenant y también se puede ejecutar manualmente en tenants existentes.

## Estructura de Directorios

```
database/migrations/projects/
├── Common/                    # Migraciones comunes a todos los proyectos
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   └── 0001_01_01_000002_create_jobs_table.php
└── SportCompetition/         # Migraciones específicas del proyecto
    └── 2024_01_01_000001_create_sport_competition_tables.php
```

## Ejecución Automática

Al crear un nuevo tenant, las migraciones se ejecutan automáticamente:

1. Se crea el tenant con un `current_project` asignado
2. El evento `TenantCreated` dispara el Job `MigrateProjectDatabase`
3. Se ejecutan migraciones en este orden:
   - Migraciones de `/database/migrations/projects/Common/`
   - Migraciones de `/database/migrations/projects/{getPathMigration()}/`

## Comando Manual: `tenants:migrate-project`

Cuando agregues **nuevas migraciones** después de que los tenants ya están creados, usa este comando.

### Sintaxis Básica

```bash
php artisan tenants:migrate-project
```

### Opciones Disponibles

#### 1. **Migrar todos los tenants**
```bash
php artisan tenants:migrate-project
```
Ejecuta migraciones Common + específicas en todos los tenants.

#### 2. **Migrar tenants específicos por ID**
```bash
php artisan tenants:migrate-project --tenant=1 --tenant=2 --tenant=3
```
Solo ejecuta en los tenants con ID 1, 2 y 3.

#### 3. **Migrar tenants de un proyecto específico**
```bash
php artisan tenants:migrate-project --project=sport-competition
```
Solo ejecuta en tenants que tienen `current_project = "sport-competition"`.

#### 4. **Solo migraciones Common**
```bash
php artisan tenants:migrate-project --common
```
Ejecuta únicamente las migraciones del folder `Common`, ignora las específicas del proyecto.

#### 5. **Fresh (eliminar y recrear tablas)**
```bash
php artisan tenants:migrate-project --fresh
```
⚠️ **CUIDADO**: Elimina todas las tablas y las recrea. **Se pierden todos los datos**.

#### 6. **Con seeders**
```bash
php artisan tenants:migrate-project --seed
```
Después de ejecutar las migraciones, ejecuta los seeders.

### Combinaciones Útiles

#### Migrar solo un tenant con fresh y seed
```bash
php artisan tenants:migrate-project --tenant=5 --fresh --seed
```

#### Migrar todos los tenants de SportCompetition
```bash
php artisan tenants:migrate-project --project=sport-competition
```

#### Actualizar solo migraciones Common en todos
```bash
php artisan tenants:migrate-project --common
```

#### Recrear base de datos de un tenant específico
```bash
php artisan tenants:migrate-project --tenant=3 --fresh --seed
```

## Ejemplo de Flujo de Trabajo

### Escenario: Agregar nueva tabla a SportCompetition

1. **Crear nueva migración**
```bash
# En database/migrations/projects/SportCompetition/
touch 2024_01_15_000001_create_referees_table.php
```

2. **Ejecutar en tenants existentes**
```bash
php artisan tenants:migrate-project --project=sport-competition
```

3. **Verificar resultados**
El comando mostrará:
```
Se ejecutarán migraciones en X tenant(s)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📦 Tenant: Mi Cliente (ID: 1)
   Proyecto: sport-competition

   🔄 Migrando [Common] (3 archivo(s))...
      Nothing to migrate.
   ✓ [Common] completado
   
   🔄 Migrando [sport-competition] (2 archivo(s))...
      Migrating: 2024_01_15_000001_create_referees_table
      Migrated:  2024_01_15_000001_create_referees_table (45.23ms)
   ✓ [sport-competition] completado

✅ Tenant 1 completado

✅ Proceso de migraciones completado
```

## Logs

El sistema registra todo en `storage/logs/laravel.log`:

```
[INFO] Iniciando migraciones para tenant: 1 - Proyecto: sport-competition
[INFO] Ejecutando migraciones [Common] desde: /path/to/database/migrations/projects/Common
[INFO] Migraciones [Common] completadas exitosamente
[INFO] Ejecutando migraciones [sport-competition] desde: /path/to/database/migrations/projects/SportCompetition
[INFO] Migraciones [sport-competition] completadas exitosamente
[INFO] Migraciones completadas para tenant: 1
```

## Mejores Prácticas

### ✅ Hacer

- Probar migraciones primero en un tenant de prueba: `--tenant=999`
- Usar `--common` cuando solo agregues migraciones comunes
- Hacer backup antes de usar `--fresh`
- Versionar las migraciones en Git

### ❌ No hacer

- Usar `--fresh` en producción sin backup
- Modificar migraciones ya ejecutadas (crear nuevas en su lugar)
- Ejecutar migraciones manualmente con `php artisan migrate` dentro de un tenant

## Troubleshooting

### Error: "No se encontraron tenants"
- Verifica que existan tenants con el proyecto especificado
- Usa `php artisan tinker` y ejecuta: `App\Models\Tenant::all()->pluck('id', 'current_project')`

### Error: "Path no existe"
- Verifica que el método `getPathMigration()` del proyecto devuelva el nombre correcto del folder
- Confirma que el directorio exista en `database/migrations/projects/`

### Migraciones no se ejecutan
- Verifica que los archivos tengan extensión `.php`
- Revisa la tabla `migrations` dentro del schema del tenant para ver qué ya se ejecutó
- Usa `--fresh` para reiniciar (⚠️ borra datos)

## Agregar Nuevo Proyecto

1. Crear directorio de migraciones:
```bash
mkdir database/migrations/projects/MiNuevoProyecto
```

2. Actualizar clase del proyecto:
```php
public function getPathMigration(): string
{
    return "MiNuevoProyecto";
}
```

3. Agregar migraciones específicas en ese folder

4. Registrar proyecto en `ProjectManager`

5. Los nuevos tenants con ese proyecto ejecutarán automáticamente esas migraciones
