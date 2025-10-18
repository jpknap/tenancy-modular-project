# 🏗️ Patrón Repository - Documentación

## 📋 Estructura Implementada

```
app/Common/Repository/
├── Contracts/
│   ├── RepositoryInterface.php      # Contrato base
│   └── CriteriaInterface.php        # Para filtros (futuro)
├── BaseRepository.php               # Implementación base
└── RepositoryManager.php            # Gestor de repositorios

app/Projects/Landlord/Repositories/
├── UserRepository.php               # Repositorio de usuarios
└── TenantRepository.php             # Repositorio de tenants
```

## ✅ Principios SOLID Aplicados

### 1. **Single Responsibility Principle (SRP)**
- Cada repositorio maneja **solo** la lógica de acceso a datos de un modelo
- AdminAdapter maneja lógica de admin
- Repository maneja lógica de persistencia

### 2. **Open/Closed Principle (OCP)**
- `BaseRepository` es extensible sin modificar su código
- Nuevos repositorios heredan funcionalidad base

### 3. **Liskov Substitution Principle (LSP)**
- Cualquier `RepositoryInterface` puede sustituirse por otro
- Facilita testing con mocks

### 4. **Interface Segregation Principle (ISP)**
- `RepositoryInterface` contiene solo métodos esenciales
- No obliga a implementar métodos innecesarios

### 5. **Dependency Inversion Principle (DIP)**
- AdminAdapter depende de la abstracción (RepositoryInterface)
- No depende de implementaciones concretas

## 🚀 Uso

### 1. Uso Directo del Repositorio

```php
use App\Projects\Landlord\Repositories\UserRepository;

class SomeController
{
    public function __construct(
        private UserRepository $userRepository
    ) {}
    
    public function index()
    {
        // Métodos básicos
        $users = $this->userRepository->all();
        $user = $this->userRepository->find(1);
        $newUser = $this->userRepository->create(['name' => 'John']);
        
        // Métodos específicos
        $user = $this->userRepository->findByEmail('john@example.com');
        $activeUsers = $this->userRepository->getActiveUsers();
    }
}
```

### 2. Uso con RepositoryManager

```php
use App\Common\Repository\RepositoryManager;

class SomeService
{
    public function __construct(
        private RepositoryManager $repositories
    ) {}
    
    public function process()
    {
        // Acceso con get()
        $users = $this->repositories->get('user')->all();
        
        // Acceso mágico
        $tenants = $this->repositories->tenant->all();
    }
}
```

### 3. Integración con AdminAdapter

```php
// En UserAdmin.php
public function repository(): string
{
    return UserRepository::class;
}

// Automáticamente usa el repositorio
$admin = new UserAdmin();
$users = $admin->getAll();        // Usa UserRepository::all()
$user = $admin->find(1);          // Usa UserRepository::find()
$paginated = $admin->paginate();  // Usa UserRepository::paginate()
```

## 📝 Crear Nuevo Repositorio

### Paso 1: Crear la clase del repositorio

```php
<?php

namespace App\Projects\Landlord\Repositories;

use App\Common\Repository\BaseRepository;
use App\Models\Product;

class ProductRepository extends BaseRepository
{
    protected function model(): string
    {
        return Product::class;
    }
    
    // Métodos específicos del modelo
    public function findBySlug(string $slug)
    {
        return $this->findOneBy('slug', $slug);
    }
    
    public function getPublished()
    {
        return $this->model->where('published', true)->get();
    }
}
```

### Paso 2: Registrar en AppServiceProvider

```php
// En register()
$manager->register('product', \App\Projects\Landlord\Repositories\ProductRepository::class);
```

### Paso 3: Usar en AdminAdapter

```php
class ProductAdmin extends AdminBaseAdapter
{
    public function repository(): string
    {
        return ProductRepository::class;
    }
}
```

## 🎯 Ventajas de esta Implementación

✅ **Separación de responsabilidades** - Lógica de datos aislada  
✅ **Testeable** - Fácil de mockear en tests  
✅ **Reutilizable** - Misma lógica en múltiples lugares  
✅ **Mantenible** - Cambios centralizados  
✅ **Escalable** - Fácil agregar nuevos repositorios  
✅ **Limpio** - Sin complejidad innecesaria  

## 📚 Métodos Disponibles en BaseRepository

### Métodos CRUD Básicos
- `all()` - Obtiene todos los registros
- `find($id)` - Busca por ID (retorna null si no existe)
- `findOrFail($id)` - Busca por ID (lanza excepción si no existe)
- `create($data)` - Crea un nuevo registro
- `update($id, $data)` - Actualiza un registro
- `delete($id)` - Elimina un registro
- `paginate($perPage)` - Obtiene registros paginados

### Métodos Helper
- `findBy($field, $value)` - Busca por campo específico
- `findOneBy($field, $value)` - Busca el primero que coincida
- `getModel()` - Obtiene la instancia del modelo

## 🔄 Flujo de Datos

```
Controller/Service
    ↓
AdminAdapter (getAll/find/paginate)
    ↓
Repository (all/find/paginate)
    ↓
Eloquent Model
    ↓
Database
```

## 🧪 Testing

```php
use App\Projects\Landlord\Repositories\UserRepository;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }
    
    public function test_can_create_user()
    {
        $user = $this->repository->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }
}
```

## 🎨 Extensiones Futuras (Opcionales)

Si necesitas más funcionalidad, puedes agregar:

1. **Criteria Pattern** - Para filtros complejos reutilizables
2. **Caché** - Agregar caching automático
3. **Eventos** - Disparar eventos en CRUD
4. **Soft Deletes** - Manejo especial de eliminación suave
5. **Query Scopes** - Scopes reutilizables

Pero mantén la simplicidad hasta que realmente lo necesites (YAGNI).
