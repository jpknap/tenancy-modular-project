# 🔄 Service Layer Pattern - Documentación

## 📋 Arquitectura Implementada

```
app/Common/Service/
└── TransactionService.php       # Servicio base para transacciones

app/Projects/Landlord/Services/
└── TenantService.php            # Servicio de lógica de negocio

app/Projects/Landlord/Http/Controller/Admin/
└── TenantServiceController.php  # Controlador que usa el servicio
```

## 🎯 Patrón Service Layer

### **Responsabilidades por Capa**

```
┌─────────────────────────────────────────────────────┐
│ Controller (HTTP Layer)                             │
│ - Recibe requests                                   │
│ - Valida entrada (FormRequest)                     │
│ - Llama al Service                                  │
│ - Retorna respuesta HTTP                           │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ Service (Business Logic Layer)                      │
│ - Lógica de negocio compleja                       │
│ - Orquesta múltiples repositorios                  │
│ - Maneja transacciones                             │
│ - Ejecuta validaciones de negocio                  │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ Repository (Data Access Layer)                      │
│ - CRUD simple                                       │
│ - Queries específicas                              │
│ - Acceso a base de datos                           │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│ Database                                            │
└─────────────────────────────────────────────────────┘
```

## ✅ Cuándo Usar Cada Capa

### **Repository** - Operaciones Simples
```php
// ✅ Usar directamente el Repository
public function store(TenantFormRequest $request)
{
    $tenant = $tenantRepository->create($request->validated());
    return redirect()->back();
}
```

**Cuándo usar**:
- CRUD simple de una sola entidad
- No hay lógica de negocio compleja
- No involucra múltiples entidades

### **Service** - Operaciones Complejas
```php
// ✅ Usar Service para lógica compleja
public function store(TenantFormRequest $request, TenantService $service)
{
    $result = $service->createTenantWithAdmin(
        $request->getTenantData(),
        $request->getAdminData()
    );
    return redirect()->back();
}
```

**Cuándo usar**:
- Múltiples entidades involucradas
- Lógica de negocio compleja
- Necesitas transacciones
- Operaciones que deben ser atómicas

## 🔄 TransactionService - Métodos Disponibles

### 1. **execute()** - Transacción Simple

```php
use App\Common\Repository\Service\TransactionService;

$transactionService = app(TransactionService::class);

$result = $transactionService->execute(function () use ($data) {
    $tenant = $tenantRepo->create($data['tenant']);
    $user = $userRepo->create($data['user']);
    return ['tenant' => $tenant, 'user' => $user];
});
```

**Características**:
- ✅ Rollback automático si falla
- ✅ Logging de errores
- ✅ Retorna el resultado de la operación

### 2. **executeMultiple()** - Múltiples Operaciones

```php
$results = $transactionService->executeMultiple([
    'tenant' => fn() => $tenantRepo->create($tenantData),
    'user' => fn() => $userRepo->create($userData),
    'settings' => fn() => $settingsRepo->create($settingsData),
]);

// $results = [
//     'tenant' => Tenant,
//     'user' => User,
//     'settings' => Settings
// ]
```

**Características**:
- ✅ Ejecuta operaciones en orden
- ✅ Retorna array con resultados nombrados
- ✅ Si falla una, rollback de todas

### 3. **executeWithRetry()** - Con Reintentos

```php
$result = $transactionService->executeWithRetry(
    callback: function () use ($data) {
        return $tenantRepo->create($data);
    },
    maxAttempts: 3
);
```

**Características**:
- ✅ Reintenta en caso de deadlock
- ✅ Backoff exponencial
- ✅ Logging de reintentos

### 4. **executeWithRollbackHandler()** - Con Handler Personalizado

```php
$result = $transactionService->executeWithRollbackHandler(
    callback: function () use ($data) {
        $tenant = $tenantRepo->create($data);
        $this->sendWelcomeEmail($tenant);
        return $tenant;
    },
    onRollback: function ($exception) {
        Log::error('Failed to create tenant', ['error' => $exception->getMessage()]);
        // Lógica de compensación
    }
);
```

## 📝 Ejemplos de Uso Completos

### Ejemplo 1: Crear Tenant con Admin

```php
// TenantService.php
public function createTenantWithAdmin(array $tenantData, array $adminData): array
{
    return $this->transactionService->execute(function () use ($tenantData, $adminData) {
        // 1. Crear tenant
        $tenant = $this->tenantRepository->create($tenantData);

        // 2. Crear admin
        $adminData['tenant_id'] = $tenant->id;
        $admin = $this->userRepository->create($adminData);

        // 3. Asignar rol
        $admin->assignRole('admin');

        // 4. Configuraciones por defecto
        $this->setupDefaultSettings($tenant);

        return ['tenant' => $tenant, 'admin' => $admin];
    });
}

// Controller
public function store(Request $request, TenantService $service)
{
    $result = $service->createTenantWithAdmin(
        tenantData: $request->input('tenant'),
        adminData: $request->input('admin')
    );

    return redirect()->back()->with('success', 'Tenant creado');
}
```

### Ejemplo 2: Actualizar Múltiples Entidades

```php
// TenantService.php
public function updateTenantWithUsers(int $tenantId, array $tenantData, array $usersData): array
{
    return $this->transactionService->executeMultiple([
        'tenant' => fn() => $this->tenantRepository->update($tenantId, $tenantData),
        'users' => fn() => $this->updateUsers($usersData),
        'settings' => fn() => $this->updateSettings($tenantId, $tenantData['settings'] ?? []),
    ]);
}

private function updateUsers(array $usersData): array
{
    $updated = [];
    foreach ($usersData as $userId => $userData) {
        $updated[$userId] = $this->userRepository->update($userId, $userData);
    }
    return $updated;
}

// Controller
public function update(int $id, Request $request, TenantService $service)
{
    $result = $service->updateTenantWithUsers(
        tenantId: $id,
        tenantData: $request->input('tenant'),
        usersData: $request->input('users', [])
    );

    return response()->json(['success' => true, 'data' => $result]);
}
```

### Ejemplo 3: Migración de Datos

```php
// TenantService.php
public function migrateUsers(int $fromTenantId, int $toTenantId, array $userIds): array
{
    return $this->transactionService->execute(function () use ($fromTenantId, $toTenantId, $userIds) {
        $migratedUsers = [];

        foreach ($userIds as $userId) {
            $user = $this->userRepository->find($userId);

            if ($user && $user->tenant_id === $fromTenantId) {
                // Actualizar tenant_id
                $this->userRepository->update($userId, [
                    'tenant_id' => $toTenantId,
                ]);

                // Registrar en log de migración
                $this->logMigration($user, $fromTenantId, $toTenantId);

                $migratedUsers[] = $user;
            }
        }

        return $migratedUsers;
    });
}
```

### Ejemplo 4: Eliminación en Cascada

```php
// TenantService.php
public function deleteTenantWithRelations(int $tenantId): bool
{
    return $this->transactionService->execute(function () use ($tenantId) {
        $tenant = $this->tenantRepository->find($tenantId);

        if (!$tenant) {
            throw new \Exception("Tenant not found");
        }

        // 1. Eliminar usuarios
        foreach ($tenant->users as $user) {
            $this->userRepository->delete($user->id);
        }

        // 2. Eliminar configuraciones
        $this->deleteSettings($tenant);

        // 3. Eliminar archivos
        $this->deleteFiles($tenant);

        // 4. Eliminar tenant
        return $this->tenantRepository->delete($tenantId);
    });
}
```

## 🎯 Ventajas del Patrón

### ✅ Separación de Responsabilidades
- Controller: Solo HTTP
- Service: Solo lógica de negocio
- Repository: Solo persistencia

### ✅ Transacciones Atómicas
- Todo o nada
- Rollback automático
- Consistencia de datos

### ✅ Reutilización
- Lógica de negocio reutilizable
- Misma lógica en API, Web, CLI

### ✅ Testeable
```php
// Test del Service (sin DB real)
public function test_create_tenant_with_admin()
{
    $tenantRepo = Mockery::mock(TenantRepository::class);
    $userRepo = Mockery::mock(UserRepository::class);
    $transactionService = new TransactionService();

    $service = new TenantService($transactionService, $tenantRepo, $userRepo);

    $tenantRepo->shouldReceive('create')->once()->andReturn($tenant);
    $userRepo->shouldReceive('create')->once()->andReturn($user);

    $result = $service->createTenantWithAdmin($tenantData, $adminData);

    $this->assertArrayHasKey('tenant', $result);
    $this->assertArrayHasKey('admin', $result);
}
```

### ✅ Mantenibilidad
- Lógica centralizada
- Fácil de modificar
- Cambios localizados

## ⚠️ Excepción: Tenancy con PostgreSQL Schemas

**IMPORTANTE**: Cuando trabajas con **PostgreSQL Schema Separation** para tenancy, NO uses transacciones en operaciones que crean/eliminan tenants:

```php
// ❌ INCORRECTO - PostgreSQL no permite CREATE/DROP SCHEMA en transacciones
public function create(array $data)
{
    return $this->transactionService->execute(function () use ($data) {
        $tenant = $this->tenantRepository->create($data);
        // CREATE SCHEMA falla aquí ❌
        return $tenant;
    });
}

// ✅ CORRECTO - Sin transacción para permitir CREATE SCHEMA
public function create(array $data)
{
    // Sin DB::transaction()
    $tenant = $this->tenantRepository->create($tenantData);
    $this->createDomain($tenant, $subdomain);
    // El evento TenantCreated → CREATE SCHEMA (fuera de transacción) ✅
    return $tenant;
}
```

**Razón**: PostgreSQL no permite ejecutar `CREATE SCHEMA` o `DROP SCHEMA` dentro de un bloque de transacción.

El paquete `stancl/tenancy` maneja la creación del schema automáticamente mediante eventos:
1. `TenantCreated` → `CreateDatabase` job → `CREATE SCHEMA`
2. `TenantDeleted` → `DeleteDatabase` job → `DROP SCHEMA CASCADE`

## 📚 Comparación: Repository vs Service

| Aspecto | Repository | Service |
|---------|-----------|---------|
| **Responsabilidad** | Acceso a datos | Lógica de negocio |
| **Complejidad** | Simple (CRUD) | Compleja (orquestación) |
| **Transacciones** | No maneja | Sí maneja* |
| **Múltiples entidades** | No | Sí |
| **Validación** | No | Sí (validación de negocio) |
| **Ejemplo** | `create($data)` | `createTenantWithAdmin($data)` |

*Excepto en operaciones de tenancy con PostgreSQL schemas

## 🔧 Registro en Service Provider

```php
// AppServiceProvider.php
public function register(): void
{
    // TransactionService como singleton
    $this->app->singleton(TransactionService::class);

    // Services específicos
    $this->app->bind(TenantService::class, function ($app) {
        return new TenantService(
            $app->make(TransactionService::class),
            $app->make(TenantRepository::class),
            $app->make(UserRepository::class)
        );
    });
}
```

## 📖 Referencias

- [Service Layer Pattern - Martin Fowler](https://martinfowler.com/eaaCatalog/serviceLayer.html)
- [Unit of Work Pattern](https://martinfowler.com/eaaCatalog/unitOfWork.html)
- [Laravel Database Transactions](https://laravel.com/docs/database#database-transactions)

---

**✅ Patrón implementado correctamente con SOLID y mejores prácticas**
