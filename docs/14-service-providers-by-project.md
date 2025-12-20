# Service Providers por Proyecto

## 📋 Descripción

Cada proyecto tiene su propio **ServiceProvider** que registra sus repositorios y servicios **solo cuando el proyecto se inicializa**.

Esto optimiza el uso de memoria al evitar cargar recursos de proyectos no utilizados.

---

## 🏗️ Arquitectura

### **1. AppServiceProvider (Global)**
Solo registra servicios compartidos por todos los proyectos:
- `RepositoryManager`
- `TransactionService`
- `AlertManager`

**NO registra** repositorios ni servicios específicos de proyectos.

---

### **2. ServiceProviders por Proyecto**

Cada proyecto tiene su propio provider:

```
app/Projects/
├── Landlord/
│   └── Providers/
│       └── LandlordServiceProvider.php
├── ActivitiesBoard/
│   └── Providers/
│       └── ActivitiesBoardServiceProvider.php
└── SportCompetition/
    └── Providers/
        └── SportCompetitionServiceProvider.php
```

---

## 🔄 Flujo de Ejecución

```
1. Request llega
   ↓
2. ProjectInitService detecta el proyecto (Landlord o Tenant)
   ↓
3. Se llama a ProjectManager::setCurrentProject(new XxxProject())
   ↓
4. Se ejecuta XxxProject->init()
   ↓
5. Se registra el ServiceProvider del proyecto
   ↓
6. Se cargan SOLO los repositorios y servicios de ese proyecto
   ↓
7. Controller puede usar los servicios
```

---

## 📝 Ejemplo: ActivitiesBoardProject

### **ActivitiesBoardProject.php**
```php
public function init(): void
{
    $this->registerServiceProvider();  // ← Registra el provider
    $this->initMenu();
}

private function registerServiceProvider(): void
{
    $app = app();
    
    if (!$app->providerIsLoaded(ActivitiesBoardServiceProvider::class)) {
        $app->register(ActivitiesBoardServiceProvider::class);
    }
}
```

### **ActivitiesBoardServiceProvider.php**
```php
public function register(): void
{
    // Repositorios
    $manager = $this->app->make(RepositoryManager::class);
    $manager->register(Activity::class, ActivityRepository::class);
    $manager->register(User::class, UserRepository::class);

    // Servicios
    $this->app->bind(ActivityService::class, function ($app) {
        return new ActivityService(
            $app->make(ActivityRepository::class),
            $app->make(TransactionService::class)
        );
    });
}
```

---

## ✅ Ventajas

### **1. Optimización de Memoria**
- Solo se cargan repositorios del proyecto activo
- Proyectos no utilizados NO ocupan memoria
- Especialmente importante en aplicaciones con muchos proyectos

### **2. Aislamiento Completo**
- Cada proyecto es 100% independiente
- No hay "contaminación" entre proyectos
- Fácil agregar/remover proyectos

### **3. Patrón Laravel Estándar**
- Usa ServiceProviders (patrón oficial)
- Compatible con `php artisan config:cache`
- Fácil de entender para desarrolladores Laravel

### **4. Lazy Loading**
- Los providers se registran bajo demanda
- Sin overhead en el bootstrap inicial
- Mejor performance en aplicaciones grandes

---

## 🎯 Casos de Uso

### **Caso 1: Tenant con ActivitiesBoard**
```
Request: http://miempresa.localhost
  ↓
Se detecta tenant "miempresa"
  ↓
current_project = "activities-board"
  ↓
Se inicializa ActivitiesBoardProject
  ↓
Se registra ActivitiesBoardServiceProvider
  ↓
✅ Solo Activity y User de ActivitiesBoard en memoria
```

### **Caso 2: Landlord**
```
Request: http://localhost
  ↓
No hay tenant inicializado
  ↓
Se inicializa LandlordProject
  ↓
Se registra LandlordServiceProvider
  ↓
✅ Solo User y Tenant de Landlord en memoria
```

---

## 🔧 Cómo Agregar un Nuevo Proyecto

### **1. Crear el ServiceProvider**
```php
// app/Projects/MiProyecto/Providers/MiProyectoServiceProvider.php

namespace App\Projects\MiProyecto\Providers;

use Illuminate\Support\ServiceProvider;

class MiProyectoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar repositorios
        $manager = $this->app->make(RepositoryManager::class);
        $manager->register(MiModelo::class, MiRepository::class);

        // Registrar servicios
        $this->app->bind(MiService::class, function ($app) {
            return new MiService(
                $app->make(MiRepository::class),
                $app->make(TransactionService::class)
            );
        });
    }
}
```

### **2. Modificar el Proyecto**
```php
// app/Projects/MiProyecto/MiProyectoProject.php

public function init(): void
{
    $this->registerServiceProvider();
    $this->initMenu();
}

private function registerServiceProvider(): void
{
    $app = app();
    
    if (!$app->providerIsLoaded(MiProyectoServiceProvider::class)) {
        $app->register(MiProyectoServiceProvider::class);
    }
}
```

---

## 📊 Comparación con Registro Global

| Aspecto | AppServiceProvider Global | ServiceProvider por Proyecto |
|---------|---------------------------|------------------------------|
| **Memoria** | ❌ Carga todo siempre | ✅ Solo proyecto activo |
| **Performance** | ❌ Bootstrap pesado | ✅ Lazy loading |
| **Aislamiento** | ⚠️ Parcial | ✅ Total |
| **Mantenibilidad** | ❌ Un archivo gigante | ✅ Un archivo por proyecto |
| **Escalabilidad** | ❌ Crece linealmente | ✅ Constante por request |

---

## 🧪 Testing

Para testing, puedes registrar el provider manualmente:

```php
class ActivityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Registrar el provider del proyecto a testear
        $this->app->register(ActivitiesBoardServiceProvider::class);
    }
    
    public function test_can_create_activity()
    {
        $service = app(ActivityService::class);
        $activity = $service->create(['name' => 'Test']);
        
        $this->assertDatabaseHas('activities', ['name' => 'Test']);
    }
}
```

---

## 🎓 Resumen

✅ **Cada proyecto gestiona sus propios recursos**  
✅ **Solo se carga lo necesario en memoria**  
✅ **Patrón Laravel estándar (ServiceProviders)**  
✅ **Escalable a cualquier número de proyectos**  
✅ **Aislamiento total entre proyectos**  

Esta arquitectura garantiza que tu aplicación multi-proyecto sea **eficiente, escalable y mantenible**.
