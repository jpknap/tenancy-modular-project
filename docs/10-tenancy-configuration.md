# 🏢 Configuración Multi-Tenancy - PostgreSQL Schema Separation

## 📋 Arquitectura de Tenancy

Este proyecto implementa **Multi-Tenancy** usando el paquete `stancl/tenancy` con **PostgreSQL Schema Separation**.

### 🎯 Estrategia: Schema Separation

Cada tenant tiene su propio **schema** dentro de la misma base de datos PostgreSQL:

```
Database: landlord
├── public (schema central)
│   ├── tenants
│   ├── domains  
│   └── users (landlord)
├── tenant1 (schema tenant 1)
│   ├── users
│   ├── posts
│   └── ... (tablas tenant)
└── tenant2 (schema tenant 2)
    ├── users
    ├── posts
    └── ... (tablas tenant)
```

## 🔧 Configuración

### 1. config/tenancy.php

```php
return [
    'tenant_model' => App\Models\Tenant::class,
    'id_generator' => null,  // IDs auto-increment
    
    'central_domains' => [
        '127.0.0.1', 
        'localhost', 
        'admin.localhost'
    ],
    
    'database' => [
        'central_connection' => 'pgsql',
        'template_tenant_connection' => 'pgsql',
        
        // ✅ CRÍTICO: Separación por schema
        'separate_by' => 'schema',
        
        'prefix' => 'tenant',
        'suffix' => '',
        
        'managers' => [
            'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLSchemaManager::class,
        ],
    ],
    
    'migration_parameters' => [
        '--force' => true,
        '--path' => [database_path('migrations/tenant')],
        '--realpath' => true,
    ],
];
```

### 2. app/Models/Tenant.php

```php
<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;

    public $incrementing = true;

    protected $fillable = [
        'name',
        'identifier',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    // ✅ CRÍTICO: Define columnas normales (no en JSON)
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'identifier',
        ];
    }
}
```

### 3. Migraciones

**database/migrations/2019_09_15_000010_create_tenants_table.php:**
```php
Schema::create('tenants', function (Blueprint $table) {
    $table->id();  // bigint auto-increment
    $table->string('name');
    $table->string('identifier')->unique();
    $table->timestamps();
    $table->json('data')->nullable();
});
```

**database/migrations/2019_09_15_000020_create_domains_table.php:**
```php
Schema::create('domains', function (Blueprint $table) {
    $table->increments('id');
    $table->string('domain', 255)->unique();
    $table->bigInteger('tenant_id');
    $table->string('subdomain');
    $table->timestamps();
    
    $table->foreign('tenant_id')
        ->references('id')
        ->on('tenants')
        ->onUpdate('cascade')
        ->onDelete('cascade');
});
```

### 4. TenantService (SIN Transacciones)

```php
<?php

namespace App\Projects\Landlord\Services\Model;

use App\Common\Repository\Service\TransactionService;
use App\Projects\Landlord\Repositories\TenantRepository;
use Illuminate\Support\Str;

class TenantService
{
    public function __construct(
        private TransactionService $transactionService,
        private TenantRepository $tenantRepository
    ) {}

    // ⚠️ IMPORTANTE: NO usar transacción
    // PostgreSQL no permite CREATE SCHEMA en transacciones
    public function create(array $data)
    {
        $identifier = Str::slug($data['name']);
        $subdomain = $data['subdomain'];
        
        $tenantData = [
            'name' => $data['name'],
            'identifier' => $identifier,
            'data' => [
                'email' => $data['email'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'description' => $data['description'] ?? null,
            ],
        ];

        $tenant = $this->tenantRepository->create($tenantData);
        
        $this->createDomain($tenant, $subdomain);
        $this->setupDefaultSettings($tenant);

        return $tenant;
    }

    // ⚠️ IMPORTANTE: NO usar transacción
    // PostgreSQL no permite DROP SCHEMA en transacciones
    public function delete(int $id): bool
    {
        return $this->tenantRepository->delete($id);
    }
    
    // ✅ Las operaciones de UPDATE sí pueden usar transacciones
    public function update(int $id, array $data)
    {
        return $this->transactionService->execute(function () use ($id, $data) {
            $tenant = $this->tenantRepository->find($id);
            
            if (!$tenant) {
                throw new \Exception('Tenant not found');
            }

            $currentData = $tenant->data ?? [];
            
            $tenantData = [
                'name' => $data['name'],
                'data' => array_merge($currentData, [
                    'email' => $data['email'] ?? null,
                    'status' => $data['status'] ?? 'pending',
                    'description' => $data['description'] ?? null,
                ]),
            ];

            return $this->tenantRepository->update($id, $tenantData);
        });
    }

    private function createDomain($tenant, string $subdomain): void
    {
        $domain = $subdomain . '.' . config('app.domain', 'localhost');
        
        $tenant->domains()->create([
            'domain' => $domain,
            'subdomain' => $subdomain,
        ]);
    }

    private function setupDefaultSettings($tenant): void
    {
        // Configuraciones por defecto
    }
}
```

## 🔄 Flujo de Creación de Tenant

### 1. Usuario crea tenant desde admin

```
POST /landlord/admin/tenants
{
    "name": "Mi Empresa S.A.",
    "subdomain": "miempresa",
    "email": "admin@miempresa.com",
    "status": "active"
}
```

### 2. TenantService.create() (sin transacción)

```php
1. INSERT INTO public.tenants
   (name: "Mi Empresa S.A.", identifier: "mi-empresa-sa")
   → tenant_id: 3

2. INSERT INTO public.domains
   (tenant_id: 3, subdomain: "miempresa", domain: "miempresa.localhost")
```

### 3. Evento TenantCreated

```php
Events\TenantCreated → JobPipeline
    ↓
Jobs\CreateDatabase
    → CREATE SCHEMA "tenant3"  ✅ (fuera de transacción)
    ↓
Jobs\MigrateDatabase
    → SET search_path TO "tenant3"
    → Ejecuta: database/migrations/tenant/*.php
    → CREATE TABLE tenant3.users, tenant3.posts, etc.
```

### 4. Resultado

```sql
-- Schema creado
SELECT schema_name FROM information_schema.schemata;
-- public, tenant1, tenant2, tenant3 ✅

-- Tablas en el schema
SELECT tablename FROM pg_tables WHERE schemaname = 'tenant3';
-- migrations, users, posts, ... ✅
```

## 🌐 Identificación de Tenant

### Por Subdomain

```
URL: miempresa.localhost
    ↓
Middleware: InitializeTenancyBySubdomain
    ↓
Query: SELECT * FROM domains WHERE subdomain = 'miempresa'
    ↓
Tenant identificado: tenant_id = 3
    ↓
SET search_path TO "tenant3"
    ↓
Todas las queries van al schema tenant3 ✅
```

### Dominios Centrales

```php
'central_domains' => [
    'localhost',           // Panel landlord
    'admin.localhost',     // Admin landlord
]
```

Estos dominios NO inicializan tenancy → usan schema `public`.

## ⚠️ Restricciones de PostgreSQL

### CREATE/DROP SCHEMA NO en Transacciones

```sql
-- ❌ FALLA
BEGIN;
    CREATE SCHEMA "tenant4";
    -- ERROR: CREATE SCHEMA cannot run inside a transaction block
COMMIT;

-- ✅ FUNCIONA
CREATE SCHEMA "tenant4";
```

**Solución**: 
- No usar `DB::transaction()` en `TenantService::create()` y `delete()`
- El paquete `stancl/tenancy` ejecuta los jobs fuera de transacciones

## 📝 Comandos Útiles

### Crear tenant manualmente

```bash
php artisan tinker

$tenant = Tenant::create([
    'name' => 'Test Tenant',
    'identifier' => 'test-tenant',
    'data' => ['email' => 'test@example.com', 'status' => 'active']
]);

$tenant->domains()->create([
    'domain' => 'test.localhost',
    'subdomain' => 'test'
]);
```

### Listar tenants

```bash
php artisan tenants:list
```

### Ejecutar migraciones en todos los tenants

```bash
php artisan tenants:migrate
```

### Ejecutar comando en un tenant específico

```bash
php artisan tenants:run cache:clear --tenant=3
```

## 🧪 Testing

### Prueba de creación de schema

```bash
# Crear tenant y verificar schema
php artisan tinker
>>> $tenant = Tenant::create(['name' => 'Test', 'identifier' => 'test', 'data' => []]);
>>> $tenant->domains()->create(['domain' => 'test.localhost', 'subdomain' => 'test']);

# Verificar en PostgreSQL
SELECT schema_name FROM information_schema.schemata WHERE schema_name LIKE 'tenant%';
```

## 🔗 Referencias

- [Tenancy for Laravel - Docs](https://tenancyforlaravel.com/docs)
- [PostgreSQL Schema Separation](https://tenancyforlaravel.com/docs/v2/postgres-schema-separation)
- [Multi-Database Tenancy](https://tenancyforlaravel.com/docs/v3/multi-database-tenancy)

---

**✅ Configuración implementada correctamente para PostgreSQL Schema Separation**
