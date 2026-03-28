# 🎛️ Admin View Config Pattern

Sistema de configuración centralizado para vistas de administración (CRUD), siguiendo los patrones Builder y Configuration Object.

## 📑 Índice

- [Introducción](#introducción)
- [Arquitectura](#arquitectura)
- [ListViewConfig](#listviewconfig)
- [CreateViewConfig](#createviewconfig)
- [EditViewConfig](#editviewconfig)
- [AdminBaseAdapter](#adminbaseadapter)
- [Ejemplos Completos](#ejemplos-completos)

## 🎯 Introducción

El patrón **Admin View Config** centraliza toda la configuración de las vistas de administración en objetos de configuración reutilizables, evitando repetir código en controladores y vistas.

### Problema que Resuelve

**❌ Antes:**
```php
// Controller
public function create() {
    $formRequest = new TenantFormRequest();
    return view('create', [
        'form' => $formRequest->getFormBuilder(),
        'title' => 'Crear Tenant',
        'submitLabel' => 'Guardar',
        'cancelRoute' => 'tenants.list',
    ]);
}

// Vista repetitiva en cada template
```

**✅ Ahora:**
```php
// Controller
public function create() {
    $config = $this->admin->getCreateViewConfig();
    return view('create', ['config' => $config]);
}

// Config centralizada en Adapter
```

## 🏗️ Arquitectura

```
app/Common/Admin/Config/
├── ListViewConfig.php       # Configuración de listados
├── CreateViewConfig.php     # Configuración de formularios create
├── EditViewConfig.php       # Configuración de formularios edit
└── (futuro) ShowViewConfig, DeleteViewConfig...

app/Common/Admin/Adapter/
└── AdminBaseAdapter.php     # Implementa getListViewConfig(), getCreateViewConfig(), getEditViewConfig()

app/Projects/{Project}/Adapters/Admin/
└── {Model}Admin.php         # Personaliza configs por modelo
```

### Flujo de Datos

```
┌────────────────────────────────────────────────────────────┐
│                     AdminBaseAdapter                       │
├────────────────────────────────────────────────────────────┤
│                                                            │
│  getListViewConfig()      →  ListViewConfig               │
│  getCreateViewConfig()    →  CreateViewConfig             │
│  getEditViewConfig($item) →  EditViewConfig               │
│                                                            │
└────────────────────────────────────────────────────────────┘
                              ↓
┌────────────────────────────────────────────────────────────┐
│                      AdminController                       │
├────────────────────────────────────────────────────────────┤
│                                                            │
│  list()       → usa $config (ListViewConfig)              │
│  create()     → usa $config (CreateViewConfig)            │
│  edit($id)    → usa $config (EditViewConfig)              │
│  destroy($id) → elimina y redirecciona                    │
│                                                            │
└────────────────────────────────────────────────────────────┘
                              ↓
┌────────────────────────────────────────────────────────────┐
│                          Vista                             │
├────────────────────────────────────────────────────────────┤
│                                                            │
│  list.blade.php    → $config->getColumns()                │
│  create.blade.php  → $config->getFormBuilder()            │
│  edit.blade.php    → $config->getFormBuilder() + item     │
│                                                            │
└────────────────────────────────────────────────────────────┘
```

## 📊 ListViewConfig

Configuración para vistas de listado (tablas con datos).

### Estructura

```php
namespace App\Common\Admin\Config;

class ListViewConfig
{
    private array $columns = [];
    private array $actions = [];
    private array $statCards = [];
    private int $perPage = 15;
    private string $emptyMessage = 'No hay registros';
}
```

### Métodos Disponibles

#### Columnas

```php
// Agregar una columna
$config->addColumn('name', 'Nombre', [
    'sortable' => true,
    'searchable' => true,
    'class' => 'text-center',
]);

// Agregar múltiples columnas
$config->columns([
    'id' => ['label' => 'ID', 'sortable' => true],
    'name' => ['label' => 'Nombre', 'searchable' => true],
    'status' => ['label' => 'Estado', 'format' => 'badge'],
    'created_at' => ['label' => 'Creado', 'format' => 'datetime'],
]);

// Obtener columnas
$columns = $config->getColumns(); // ListColumn[]
```

#### Acciones

```php
// Agregar acción
$config->addAction('Editar', 'tenants.edit', [
    'icon' => 'bi-pencil text-primary',
    'route_params' => ['id' => 'id'],
]);

// Acción con confirmación
$config->addAction('Eliminar', 'tenants.destroy', [
    'icon' => 'bi-trash text-danger',
    'type' => 'form',
    'confirm' => true,
    'confirm_message' => '¿Está seguro?',
    'route_params' => ['id' => 'id'],
]);

// Obtener acciones
$actions = $config->getActions(); // ListAction[]
```

#### StatCards (Tarjetas de Estadísticas)

```php
// Agregar tarjeta
$config->addStatCard('Total Registros', 0, [
    'icon' => 'bi-building',
    'color' => 'primary',
    'value_resolver' => fn($items) => $items->total(),
]);

// Obtener tarjetas
$cards = $config->getStatCards(); // StatCard[]
$hasCards = $config->hasStatCards(); // bool
```

#### Configuración General

```php
// Paginación
$config->perPage(20);
$perPage = $config->getPerPage(); // int

// Mensaje vacío
$config->emptyMessage('No hay datos disponibles');
$message = $config->getEmptyMessage(); // string
```

### Ejemplo Completo

```php
public function getListViewConfig(): ListViewConfig
{
    $config = new ListViewConfig();

    // StatCards
    $config->addStatCard('Total', 0, [
        'icon' => 'bi-building',
        'color' => 'primary',
        'value_resolver' => fn($items) => $items->total(),
    ]);

    // Columnas
    $config->columns([
        'id' => ['label' => 'ID', 'sortable' => true, 'class' => 'text-center'],
        'name' => ['label' => 'Nombre', 'sortable' => true, 'searchable' => true],
        'email' => ['label' => 'Email', 'sortable' => true],
        'status' => ['label' => 'Estado', 'format' => 'badge'],
        'created_at' => ['label' => 'Fecha Creación', 'format' => 'datetime'],
    ]);

    // Acciones
    $config->addAction('Ver', 'tenants.show', [
        'icon' => 'bi-eye text-info',
        'route_params' => ['id' => 'id'],
    ]);

    $config->addAction('Editar', 'tenants.edit', [
        'icon' => 'bi-pencil text-primary',
        'route_params' => ['id' => 'id'],
    ]);

    $config->addAction('Eliminar', 'tenants.destroy', [
        'icon' => 'bi-trash text-danger',
        'type' => 'form',
        'confirm' => true,
        'confirm_message' => '¿Está seguro de eliminar este registro?',
        'route_params' => ['id' => 'id'],
    ]);

    // Configuración
    $config->perPage(15);
    $config->emptyMessage('No hay registros disponibles');

    return $config;
}
```

## 📝 CreateViewConfig

Configuración para vistas de creación/edición de formularios.

### Estructura

```php
namespace App\Common\Admin\Config;

use App\Common\FormBuilder\FormBuilder;

class CreateViewConfig
{
    private FormBuilder $formBuilder;
    private string $title = 'Crear Nuevo';
    private string $submitLabel = 'Guardar';
    private ?string $cancelRoute = null;
    private string $successMessage = 'Registro creado exitosamente';

    public function __construct(FormBuilder $formBuilder) { }
}
```

### Métodos Disponibles

```php
// Título de la página
$config->title('Crear Nuevo Tenant');
$title = $config->getTitle(); // string

// Label del botón submit
$config->submitLabel('Crear Tenant');
$label = $config->getSubmitLabel(); // string

// Ruta de cancelar
$config->cancelRoute('tenants.list');
$route = $config->getCancelRoute(); // ?string

// Mensaje de éxito
$config->successMessage('Tenant creado exitosamente');
$message = $config->getSuccessMessage(); // string

// FormBuilder
$formBuilder = $config->getFormBuilder(); // FormBuilder
```

### Ejemplo Completo

```php
public function getCreateViewConfig(): CreateViewConfig
{
    // Obtener configuración base del padre
    $config = parent::getCreateViewConfig();

    // Personalizar
    $config
        ->title('Crear Nuevo Tenant')
        ->submitLabel('Crear Tenant')
        ->cancelRoute('tenants.list')
        ->successMessage('Tenant creado exitosamente');

    return $config;
}
```

### Uso en Controller

```php
#[Route('create', methods: ['GET','POST'], name: 'create')]
public function create(Request $request)
{
    $config = $this->admin->getCreateViewConfig();
    
    if ($request->isMethod('GET')) {
        return view('landlord.create', [
            'admin' => $this->admin,
            'config' => $config,
        ]);
    }
    
    // POST: procesar formulario
    $formRequestClass = $this->admin->getFormRequest();
    $validated = app($formRequestClass)->validated();
    $serviceClass = $this->admin->getService();

    $item = app($serviceClass)->create($validated);

    return redirect()
        ->route($this->admin->getRoutePrefix() . '.list')
        ->with('success', $config->getSuccessMessage());
}
```

### Uso en Vista

```blade
{{-- create.blade.php --}}
<div class="content-card">
    <h2>{{ $config->getTitle() }}</h2>

    <form method="POST">
        @csrf
        
        {!! $config->getFormBuilder()->render() !!}
        
        <div class="d-flex justify-content-between mt-4">
            @if($config->getCancelRoute())
                <a href="{{ route($config->getCancelRoute()) }}" class="btn btn-secondary">
                    Cancelar
                </a>
            @endif
            
            <button type="submit" class="btn btn-primary">
                {{ $config->getSubmitLabel() }}
            </button>
        </div>
    </form>
</div>
```

## ✏️ EditViewConfig

Configuración para vistas de edición de formularios (similar a CreateViewConfig pero con datos pre-cargados).

### Estructura

```php
namespace App\Common\Admin\Config;

use App\Common\Admin\Form\FormBuilder;

class EditViewConfig
{
    private FormBuilder $formBuilder;
    private string $title = 'Editar';
    private string $submitLabel = 'Actualizar';
    private mixed $item = null;

    public function __construct(FormBuilder $formBuilder) { }
}
```

### Métodos Disponibles

```php
// Título de la página
$config->title('Editar Tenant: ' . $item->name);
$title = $config->getTitle(); // string

// Label del botón submit
$config->submitLabel('Actualizar');
$label = $config->getSubmitLabel(); // string

// Item a editar
$config->item($item);
$item = $config->getItem(); // mixed (el modelo)

// FormBuilder
$formBuilder = $config->getFormBuilder(); // FormBuilder
```

### Ejemplo Completo

```php
public function getEditViewConfig(mixed $item): EditViewConfig
{
    // Obtener configuración base del padre
    $config = parent::getEditViewConfig($item);

    // Personalizar con datos del item
    $config
        ->title('Editar Tenant: ' . $item->name)
        ->submitLabel('Actualizar Tenant');

    return $config;
}
```

### Uso en Controller

```php
#[Route('edit/{id}', methods: ['GET', 'PUT'], name: 'edit')]
public function edit(Request $request, $id)
{
    $item = $this->admin->find($id);
    
    if (!$item) {
        abort(404);
    }

    $config = $this->admin->getEditViewConfig($item);
    
    if ($request->isMethod('GET')) {
        return view('landlord.edit', [
            'admin' => $this->admin,
            'config' => $config,
        ]);
    }
    
    // PUT: procesar actualización
    $formRequestClass = $this->admin->getFormRequest();
    $validated = app($formRequestClass)->validated();
    $serviceClass = $this->admin->getService();

    app($serviceClass)->update($id, $validated);

    return redirect()
        ->route($this->admin->getUrlName('list'))
        ->with('success', 'Registro actualizado exitosamente');
}
```

### Uso en Vista

```blade
{{-- edit.blade.php --}}
<div class="content-card">
    <h2>{{ $config->getTitle() }}</h2>

    <form method="POST" action="{{ $admin->getUrl('edit', ['id' => $config->getItem()->id]) }}">
        @csrf
        @method('PUT')
        
        @foreach($config->getFormBuilder()->getFields() as $field)
            @php
                // Auto-poblar con datos del item
                $field['value'] = old($field['name'], data_get($config->getItem(), $field['name']));
            @endphp
            <x-form.field :field="$field" />
        @endforeach
        
        <button type="submit">{{ $config->getSubmitLabel() }}</button>
    </form>
</div>
```

### Diferencias con CreateViewConfig

| Característica | CreateViewConfig | EditViewConfig |
|----------------|------------------|----------------|
| **Título default** | 'Crear Nuevo' | 'Editar' |
| **Submit label** | 'Guardar' | 'Actualizar' |
| **Método HTTP** | POST | PUT |
| **Tiene item** | ❌ No | ✅ Sí (`getItem()`) |
| **Campos** | Vacíos | Pre-poblados |
| **Ruta** | `/create` | `/edit/{id}` |

## 🎯 AdminBaseAdapter

El `AdminBaseAdapter` proporciona implementaciones por defecto de todos los configs.

### Implementación Base

```php
abstract class AdminBaseAdapter implements AdminAdapterInterface
{
    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        $config->columns([
            'id' => 'ID',
            'created_at' => ['label' => 'Creado', 'format' => 'datetime'],
        ]);

        return $config;
    }

    public function getCreateViewConfig(): CreateViewConfig
    {
        $formRequestClass = $this->getFormRequest();
        $formRequest = new $formRequestClass();
        
        $config = new CreateViewConfig($formRequest->getFormBuilder());
        
        $config
            ->title('Crear ' . $this->getTitle())
            ->submitLabel('Guardar');
        
        return $config;
    }

    public function getEditViewConfig(mixed $item): EditViewConfig
    {
        $formRequestClass = $this->getFormRequest();
        $formRequest = new $formRequestClass();
        
        $config = new EditViewConfig($formRequest->getFormBuilder());
        
        $config
            ->title('Editar ' . $this->getTitle())
            ->submitLabel('Actualizar')
            ->item($item);
        
        return $config;
    }
}
```

### Personalización en Adapters

```php
class TenantAdmin extends AdminBaseAdapter
{
    // Personalizar ListView
    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();
        
        // Configuración específica para Tenant
        $config->columns([
            'id' => ['label' => 'ID', 'sortable' => true],
            'name' => ['label' => 'Nombre', 'searchable' => true],
        ]);
        
        return $config;
    }

    // Personalizar CreateView
    public function getCreateViewConfig(): CreateViewConfig
    {
        $config = parent::getCreateViewConfig();
        
        // Sobrescribir configuración
        $config
            ->title('Crear Nuevo Tenant')
            ->submitLabel('Crear Tenant')
            ->successMessage('Tenant creado exitosamente');
        
        return $config;
    }
}
```

## 📚 Ejemplos Completos

### Ejemplo 1: CRUD Completo de Tenant

```php
// TenantAdmin.php
class TenantAdmin extends AdminBaseAdapter
{
    protected static string $controller = TenantAdminController::class;
    protected static string $model = Tenant::class;
    protected string $routePrefix = 'tenant';

    public function getTitle(): string
    {
        return 'Tenants';
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        // StatCards
        $config->addStatCard('Total Tenants', 0, [
            'icon' => 'bi-building',
            'color' => 'primary',
            'value_resolver' => fn($items) => $items->total(),
        ]);

        $config->addStatCard('Activos', 0, [
            'icon' => 'bi-check-circle',
            'color' => 'success',
            'value_resolver' => fn($items) => $items->where('status', 'active')->count(),
        ]);

        // Columnas
        $config->columns([
            'id' => ['label' => 'ID', 'sortable' => true, 'class' => 'text-center'],
            'name' => ['label' => 'Nombre', 'sortable' => true, 'searchable' => true],
            'email' => ['label' => 'Email', 'sortable' => true],
            'status' => ['label' => 'Estado', 'format' => 'badge'],
            'created_at' => ['label' => 'Fecha', 'format' => 'datetime'],
        ]);

        // Acciones
        $config->addAction('Editar', 'landlord.admin.tenant.edit', [
            'icon' => 'bi-pencil text-primary',
            'route_params' => ['id' => 'id'],
        ]);

        $config->addAction('Eliminar', 'landlord.admin.tenant.destroy', [
            'icon' => 'bi-trash text-danger',
            'type' => 'form',
            'confirm' => true,
            'confirm_message' => '¿Está seguro de eliminar este tenant?',
            'route_params' => ['id' => 'id'],
        ]);

        $config->perPage(15);
        $config->emptyMessage('No hay tenants registrados');

        return $config;
    }

    public function getCreateViewConfig(): CreateViewConfig
    {
        $config = parent::getCreateViewConfig();

        $config
            ->title('Crear Nuevo Tenant')
            ->submitLabel('Crear Tenant')
            ->successMessage('Tenant creado exitosamente');

        return $config;
    }

    public function getFormRequest(): string
    {
        return TenantFormRequest::class;
    }

    public function getService(): string
    {
        return TenantService::class;
    }
}
```

### Ejemplo 2: CRUD Simple de User

```php
// UserAdmin.php
class UserAdmin extends AdminBaseAdapter
{
    protected static string $controller = UserAdminController::class;
    protected static string $model = User::class;
    protected string $routePrefix = 'user';

    public function getTitle(): string
    {
        return 'Usuarios';
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        // Columnas simples
        $config->columns([
            'id' => ['label' => 'ID', 'sortable' => true],
            'name' => ['label' => 'Nombre', 'searchable' => true],
            'email' => ['label' => 'Email', 'searchable' => true],
            'role' => ['label' => 'Rol', 'format' => 'badge'],
            'created_at' => ['label' => 'Registro', 'format' => 'datetime'],
        ]);

        // Acciones básicas
        $config->addAction('Editar', 'landlord.admin.user.edit', [
            'icon' => 'bi-pencil text-primary',
            'route_params' => ['id' => 'id'],
        ]);

        $config->perPage(20);

        return $config;
    }

    // CreateViewConfig usa configuración por defecto del padre
}
```

## 🔄 Sistema CRUD Completo

El sistema admin ahora tiene implementado un CRUD completo:

| Operación | Config | Controller | Vista | Estado |
|-----------|--------|------------|-------|--------|
| **List** | `ListViewConfig` | `list()` | `list.blade.php` | ✅ Completo |
| **Create** | `CreateViewConfig` | `create()` | `create.blade.php` | ✅ Completo |
| **Edit** | `EditViewConfig` | `edit($id)` | `edit.blade.php` | ✅ Completo |
| **Delete** | - | `destroy($id)` | Form en list | ✅ Completo |
| **Show** | - | - | - | ⏳ Pendiente |

### Rutas Generadas

Para cada adapter (ej. `UserAdmin` con `routePrefix = 'users'`):

```
landlord.admin.users.list     GET     /landlord/admin/users/list
landlord.admin.users.create   GET     /landlord/admin/users/create
landlord.admin.users.create   POST    /landlord/admin/users/create
landlord.admin.users.edit     GET     /landlord/admin/users/edit/{id}
landlord.admin.users.edit     PUT     /landlord/admin/users/edit/{id}
landlord.admin.users.destroy  DELETE  /landlord/admin/users/destroy/{id}
```

### Métodos de AdminController

```php
abstract class AdminController extends Controller
{
    #[Route('list', methods: ['GET'], name: 'list')]
    public function list() { ... }

    #[Route('create', methods: ['GET', 'POST'], name: 'create')]
    public function create(Request $request) { ... }

    #[Route('edit/{id}', methods: ['GET', 'PUT'], name: 'edit')]
    public function edit(Request $request, $id) { ... }

    #[Route('destroy/{id}', methods: ['DELETE'], name: 'destroy')]
    public function destroy($id) { ... }
}
```

## ✨ Ventajas del Patrón

### ✅ Centralización
- Toda la configuración en un solo lugar (Adapter)
- No repetir código en controllers ni vistas

### ✅ Consistencia
- Mismo patrón para todas las vistas (list, create, edit, show)
- API consistente entre diferentes módulos

### ✅ Flexibilidad
- Fácil personalizar por modelo
- Herencia y sobrescritura simple

### ✅ Type Safety
- Objetos tipados en lugar de arrays
- Autocompletado en IDE

### ✅ Mantenibilidad
- Cambios centralizados
- Fácil agregar nuevas funcionalidades

### ✅ Reutilización
- Configs reutilizables
- Vistas genéricas que funcionan con cualquier config

## 🎯 Patrones Aplicados

- ✅ **Builder Pattern** - Métodos fluent encadenables
- ✅ **Configuration Object** - Objetos de configuración dedicados
- ✅ **Adapter Pattern** - Adaptadores por modelo
- ✅ **Template Method** - Implementación base con sobrescritura
- ✅ **Dependency Injection** - FormBuilder inyectado en CreateViewConfig

## 📖 Ver También

- [ListView Config Pattern](./05-listview-config-pattern.md) - Detalles de ListViewConfig
- [Form Builder Pattern](./04-form-builder-pattern.md) - Constructor de formularios
- [Repository Pattern](./02-repository-pattern.md) - Acceso a datos
- [Service Layer Pattern](./03-service-layer-pattern.md) - Lógica de negocio

---

**Última actualización**: Octubre 2025
**Patrón**: Builder + Configuration Object
