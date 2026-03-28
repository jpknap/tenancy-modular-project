# 📊 ListView Configuration Pattern - Documentación

## 🎯 Objetivo

Proporcionar una configuración robusta, flexible y reutilizable para vistas de listado (tablas) con columnas, acciones, filtros y búsqueda.

## 🏗️ Arquitectura

```
app/Common/ListView/
├── ListViewConfig.php    # Configuración principal
├── ListColumn.php        # Configuración de columnas
├── ListAction.php        # Configuración de acciones
└── ListFilter.php        # Configuración de filtros
```

## 📝 Componentes

### 1. **ListViewConfig** - Configuración Principal

Objeto central que contiene toda la configuración del listado.

```php
use App\Common\ListView\ListViewConfig;

$config = new ListViewConfig();

// Columnas
$config->columns([
    'id' => 'ID',
    'name' => ['label' => 'Nombre', 'sortable' => true],
    'email' => ['label' => 'Email', 'searchable' => true],
]);

// Acciones
$config->addAction('Editar', 'users.edit', [
    'icon' => 'bi-pencil',
    'route_params' => ['id' => 'id'],
]);

// Filtros
$config->addFilter('status', 'Estado', 'select', [
    'choices' => ['active' => 'Activo', 'inactive' => 'Inactivo'],
]);

// Búsqueda
$config->search('Buscar usuarios...');

// Paginación
$config->pagination(15);

// Botón crear
$config->createButton('users.create', 'Nuevo Usuario');
```

### 2. **ListColumn** - Configuración de Columnas

Representa una columna en la tabla.

**Opciones disponibles:**

```php
$config->addColumn('email', 'Email', [
    'sortable' => true,      // Columna ordenable
    'searchable' => true,    // Columna buscable
    'format' => 'date',      // Formato: date, datetime, currency, boolean, badge
    'formatter' => fn($v) => strtoupper($v), // Función personalizada
    'class' => 'text-center', // Clase CSS para la celda
    'header_class' => 'bg-primary', // Clase CSS para el header
    'visible' => true,       // Si la columna es visible
]);
```

**Formatos predefinidos:**

- `date` → 01/12/2024
- `datetime` → 01/12/2024 14:30
- `currency` → $1,234.56
- `boolean` → Sí / No
- `badge` → Badge con colores (active, inactive, pending, completed)

**Ejemplo con formato personalizado:**

```php
$config->addColumn('price', 'Precio', [
    'formatter' => fn($value) => '$ ' . number_format($value, 2)
]);

// O con función completa
$config->addColumn('user_info', 'Usuario', [
    'formatter' => function($value, $item) {
        return "{$item->name} ({$item->email})";
    }
]);
```

### 3. **ListAction** - Configuración de Acciones

Representa un botón/enlace de acción por fila.

**Opciones disponibles:**

```php
$config->addAction('Editar', 'users.edit', [
    'type' => 'link',           // link, button, form
    'icon' => 'bi-pencil',      // Icono Bootstrap Icons
    'class' => 'btn btn-sm btn-primary', // Clases CSS
    'confirm' => true,          // Requiere confirmación
    'confirm_message' => '¿Está seguro?', // Mensaje de confirmación
    'route_params' => [         // Parámetros de la ruta
        'id' => 'id',           // Toma el campo 'id' del item
        'slug' => 'slug',
    ],
]);
```

**Tipos de acciones:**

- **`link`** - Enlace simple (GET)
- **`button`** - Botón con JavaScript
- **`form`** - Formulario (POST/DELETE) con CSRF

**Ejemplo de acción DELETE:**

```php
$config->addAction('Eliminar', 'users.destroy', [
    'icon' => 'bi-trash',
    'class' => 'btn btn-sm btn-danger',
    'type' => 'form',
    'confirm' => true,
    'confirm_message' => '¿Eliminar este usuario?',
    'route_params' => ['id' => 'id'],
]);
```

### 4. **ListFilter** - Configuración de Filtros

Representa un filtro en el listado.

**Tipos de filtros:**

```php
// Filtro de texto
$config->addFilter('search', 'Buscar', 'text', [
    'placeholder' => 'Buscar...',
]);

// Filtro select
$config->addFilter('status', 'Estado', 'select', [
    'choices' => [
        '' => 'Todos',
        'active' => 'Activo',
        'inactive' => 'Inactivo',
    ],
    'default' => 'active',
]);

// Filtro de fecha
$config->addFilter('created_from', 'Desde', 'date');

// Filtro de rango de fechas
$config->addFilter('period', 'Período', 'daterange');
```

### 5. **StatCard** - Tarjetas de Estadísticas ⭐

Representa una tarjeta de estadística en la parte superior del listado.

**Opciones disponibles:**

```php
$config->addStatCard('Total Usuarios', 0, [
    'icon' => 'bi-people',              // Icono Bootstrap Icons
    'color' => 'primary',               // primary, success, warning, danger, info, secondary
    'value_resolver' => fn($items) => $items->total(), // Función para calcular el valor
]);
```

**Colores disponibles:**
- `primary` → Azul
- `success` → Verde
- `warning` → Amarillo
- `danger` → Rojo
- `info` → Cyan
- `secondary` → Gris

**Ejemplo completo:**

```php
// Tarjeta simple con valor estático
$config->addStatCard('Total', 150, [
    'icon' => 'bi-building',
    'color' => 'primary',
]);

// Tarjeta con valor dinámico
$config->addStatCard('Activos', 0, [
    'icon' => 'bi-check-circle',
    'color' => 'success',
    'value_resolver' => function($items) {
        return $items->where('status', 'active')->count();
    },
]);

// Tarjeta con cálculo complejo
$config->addStatCard('Ingresos', 0, [
    'icon' => 'bi-cash-stack',
    'color' => 'success',
    'value_resolver' => function($items) {
        return '$' . number_format($items->sum('revenue'), 2);
    },
]);
```

## 📝 Métodos de ListViewConfig (Actualizado)

### Tarjetas de Estadísticas

```php
// Agregar una tarjeta
$config->addStatCard('Total', 100, [
    'icon' => 'bi-building',
    'color' => 'primary',
]);

// Agregar múltiples tarjetas
$config->statCards([
    [
        'title' => 'Total',
        'value' => 100,
        'options' => ['icon' => 'bi-building', 'color' => 'primary'],
    ],
    [
        'title' => 'Activos',
        'value' => 80,
        'options' => ['icon' => 'bi-check', 'color' => 'success'],
    ],
]);

// Obtener tarjetas
$cards = $config->getStatCards(); // Array de StatCard
$hasCards = $config->hasStatCards(); // bool
```

## 🎨 Ejemplo Completo con StatCards

### TenantAdmin con Estadísticas

```php
public function getListViewConfig(): ListViewConfig
{
    $config = new ListViewConfig();

    // ========== TARJETAS DE ESTADÍSTICAS ==========
    $config->addStatCard('Total Registros', 0, [
        'icon' => 'bi-building',
        'color' => 'primary',
        'value_resolver' => fn($items) => $items->total(),
    ]);

    $config->addStatCard('Activos', 0, [
        'icon' => 'bi-check-circle',
        'color' => 'success',
        'value_resolver' => fn($items) => $items->where('status', 'active')->count(),
    ]);

    $config->addStatCard('Pendientes', 0, [
        'icon' => 'bi-clock',
        'color' => 'warning',
        'value_resolver' => fn($items) => $items->where('status', 'pending')->count(),
    ]);

    $config->addStatCard('Inactivos', 0, [
        'icon' => 'bi-x-circle',
        'color' => 'danger',
        'value_resolver' => fn($items) => $items->where('status', 'inactive')->count(),
    ]);

    // ========== COLUMNAS ==========
    $config->columns([
        'id' => ['label' => 'ID', 'sortable' => true],
        'name' => ['label' => 'Nombre', 'searchable' => true],
        'status' => ['label' => 'Estado', 'format' => 'badge'],
    ]);

    return $config;
}
```

**Resultado en la Vista:**
- ✅ 4 tarjetas de estadísticas en la parte superior
- ✅ Valores calculados dinámicamente
- ✅ Iconos y colores personalizados
- ✅ Responsive (se ajustan automáticamente)

## 🎯 Ventajas de StatCards

✅ **Configurables** - Título, valor, icono, color
✅ **Dinámicas** - Valores calculados en tiempo real
✅ **Reutilizables** - Misma estructura para todos los listados
✅ **Responsive** - Se ajustan automáticamente al número de tarjetas
✅ **Visuales** - Iconos y colores Bootstrap

## 📊 Tipos de Filtros (Actualizado)

**Tipos de filtros:**

```php
// Filtro de texto
$config->addFilter('search', 'Buscar', 'text', [
    'placeholder' => 'Buscar...',
]);

// Filtro select
$config->addFilter('status', 'Estado', 'select', [
    'choices' => [
        '' => 'Todos',
        'active' => 'Activo',
        'inactive' => 'Inactivo',
    ],
    'default' => 'active',
]);

// Filtro de fecha
$config->addFilter('created_from', 'Desde', 'date');

// Filtro de rango de fechas
$config->addFilter('period', 'Período', 'daterange');
```

## 🚀 Uso en Adapters

### Ejemplo Completo: TenantAdmin

```php
<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\ListView\ListViewConfig;

class TenantAdmin extends AdminBaseAdapter
{
    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        // ========== COLUMNAS ==========
        $config->columns([
            'id' => [
                'label' => 'ID',
                'sortable' => true,
                'class' => 'text-center',
            ],
            'name' => [
                'label' => 'Nombre',
                'sortable' => true,
                'searchable' => true,
            ],
            'email' => [
                'label' => 'Email',
                'sortable' => true,
                'searchable' => true,
            ],
            'status' => [
                'label' => 'Estado',
                'format' => 'badge',
                'class' => 'text-center',
            ],
            'created_at' => [
                'label' => 'Fecha Creación',
                'format' => 'datetime',
                'sortable' => true,
            ],
        ]);

        // ========== ACCIONES ==========
        $config->addAction('Ver', 'landlord.admin.tenant.show', [
            'icon' => 'bi-eye',
            'class' => 'btn btn-sm btn-info',
            'route_params' => ['id' => 'id'],
        ]);

        $config->addAction('Editar', 'landlord.admin.tenant.edit', [
            'icon' => 'bi-pencil',
            'class' => 'btn btn-sm btn-primary',
            'route_params' => ['id' => 'id'],
        ]);

        $config->addAction('Eliminar', 'landlord.admin.tenant.destroy', [
            'icon' => 'bi-trash',
            'class' => 'btn btn-sm btn-danger',
            'type' => 'form',
            'confirm' => true,
            'confirm_message' => '¿Está seguro de eliminar este tenant?',
            'route_params' => ['id' => 'id'],
        ]);

        // ========== FILTROS ==========
        $config->addFilter('status', 'Estado', 'select', [
            'choices' => [
                '' => 'Todos',
                'active' => 'Activos',
                'inactive' => 'Inactivos',
            ],
        ]);

        $config->addFilter('created_from', 'Desde', 'date');
        $config->addFilter('created_to', 'Hasta', 'date');

        // ========== BÚSQUEDA ==========
        $config->search('Buscar por nombre o email...');

        // ========== PAGINACIÓN ==========
        $config->pagination(15);

        // ========== BOTÓN CREAR ==========
        $config->createButton('landlord.admin.tenant.new', 'Nuevo Tenant');

        // ========== MENSAJE VACÍO ==========
        $config->emptyMessage('No hay tenants registrados');

        return $config;
    }
}
```

## 📊 Métodos de ListViewConfig

### Columnas

```php
// Agregar una columna
$config->addColumn('name', 'Nombre', ['sortable' => true]);

// Agregar múltiples columnas
$config->columns([
    'id' => 'ID',
    'name' => ['label' => 'Nombre', 'sortable' => true],
]);

// Obtener columnas
$columns = $config->getColumns(); // Array de ListColumn
```

### Acciones

```php
// Agregar acción
$config->addAction('Editar', 'users.edit', [
    'icon' => 'bi-pencil',
    'route_params' => ['id' => 'id'],
]);

// Obtener acciones
$actions = $config->getActions(); // Array de ListAction
```

### Filtros

```php
// Agregar filtro
$config->addFilter('status', 'Estado', 'select', [
    'choices' => ['active' => 'Activo'],
]);

// Obtener filtros
$filters = $config->getFilters(); // Array de ListFilter
```

### Búsqueda

```php
// Configurar búsqueda
$config->search('Buscar...', true);

// Deshabilitar búsqueda
$config->search('', false);

// Obtener configuración
$placeholder = $config->getSearchPlaceholder();
$enabled = $config->showSearch();
```

### Paginación

```php
// Configurar paginación
$config->pagination(20, true);

// Deshabilitar paginación
$config->pagination(15, false);

// Obtener configuración
$perPage = $config->getPerPage();
$enabled = $config->showPagination();
```

### Botón Crear

```php
// Agregar botón crear
$config->createButton('users.create', 'Nuevo Usuario');

// Verificar si existe
$hasButton = $config->hasCreateButton();

// Obtener configuración
$route = $config->getCreateRoute();
$label = $config->getCreateLabel();
```

### Mensaje Vacío

```php
// Configurar mensaje
$config->emptyMessage('No hay datos');

// Obtener mensaje
$message = $config->getEmptyMessage();
```

## 🎨 Formatos de Columnas

### Formatos Predefinidos

```php
// Fecha: 01/12/2024
$config->addColumn('date', 'Fecha', ['format' => 'date']);

// Fecha y hora: 01/12/2024 14:30
$config->addColumn('created_at', 'Creado', ['format' => 'datetime']);

// Moneda: $1,234.56
$config->addColumn('price', 'Precio', ['format' => 'currency']);

// Booleano: Sí / No
$config->addColumn('active', 'Activo', ['format' => 'boolean']);

// Badge con colores
$config->addColumn('status', 'Estado', ['format' => 'badge']);
```

### Formato Badge

Valores soportados automáticamente:
- `active` → Badge verde "Activo"
- `inactive` → Badge gris "Inactivo"
- `pending` → Badge amarillo "Pendiente"
- `completed` → Badge azul "Completado"

### Formatter Personalizado

```php
$config->addColumn('full_name', 'Nombre Completo', [
    'formatter' => function($value, $item) {
        return "{$item->first_name} {$item->last_name}";
    }
]);

$config->addColumn('avatar', 'Avatar', [
    'formatter' => function($value) {
        return "<img src='{$value}' class='rounded-circle' width='40'>";
    }
]);
```

## ✨ Características Avanzadas

### 1. Columnas Condicionales

```php
$columns = [
    'id' => 'ID',
    'name' => 'Nombre',
];

if (auth()->user()->isAdmin()) {
    $columns['salary'] = ['label' => 'Salario', 'format' => 'currency'];
}

$config->columns($columns);
```

### 2. Acciones Condicionales

```php
$config->addAction('Editar', 'users.edit', [
    'icon' => 'bi-pencil',
    'route_params' => ['id' => 'id'],
]);

if (auth()->user()->can('delete-users')) {
    $config->addAction('Eliminar', 'users.destroy', [
        'icon' => 'bi-trash',
        'class' => 'btn btn-sm btn-danger',
        'type' => 'form',
        'confirm' => true,
    ]);
}
```

### 3. Parámetros de Ruta Dinámicos

```php
$config->addAction('Ver Perfil', 'users.profile', [
    'route_params' => [
        'id' => 'id',
        'slug' => function($item) {
            return Str::slug($item->name);
        },
    ],
]);
```

### 4. Columnas con Relaciones

```php
$config->addColumn('tenant_name', 'Tenant', [
    'formatter' => function($value, $item) {
        return $item->tenant->name ?? '-';
    }
]);
```

## 🔧 Integración con Vista

### En el Controller

```php
public function list()
{
    $config = $this->admin->getListViewConfig();
    $items = $this->admin->paginate($config->getPerPage());

    return view('landlord.list', [
        'admin' => $this->admin,
        'config' => $config,
        'items' => $items,
    ]);
}
```

### En la Vista Blade

```blade
{{-- Header con búsqueda y botón crear --}}
<div class="d-flex justify-content-between mb-3">
    @if($config->showSearch())
        <input 
            type="text" 
            class="form-control w-50" 
            placeholder="{{ $config->getSearchPlaceholder() }}"
        >
    @endif

    @if($config->hasCreateButton())
        <a href="{{ route($config->getCreateRoute()) }}" class="btn btn-primary">
            {{ $config->getCreateLabel() }}
        </a>
    @endif
</div>

{{-- Tabla --}}
<table class="table">
    <thead>
        <tr>
            @foreach($config->getColumns() as $column)
                <th class="{{ $column->getHeaderClass() }}">
                    {{ $column->getLabel() }}
                </th>
            @endforeach
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $item)
            <tr>
                @foreach($config->getColumns() as $column)
                    <td class="{{ $column->getClass() }}">
                        {!! $column->format($item->{$column->getKey()}) !!}
                    </td>
                @endforeach
                <td>
                    @foreach($config->getActions() as $action)
                        <a 
                            href="{{ $action->getUrl($item) }}" 
                            class="{{ $action->getClass() }}"
                        >
                            <i class="{{ $action->getIcon() }}"></i>
                            {{ $action->getLabel() }}
                        </a>
                    @endforeach
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100" class="text-center">
                    {{ $config->getEmptyMessage() }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Paginación --}}
@if($config->showPagination())
    {{ $items->links() }}
@endif
```

## 📚 Comparación: Antes vs Después

### ❌ Antes (Array Simple)

```php
public function getListableAttributes(): array
{
    return ['id', 'name', 'email', 'created_at'];
}
```

**Limitaciones:**
- Solo nombres de columnas
- Sin formato
- Sin acciones
- Sin filtros
- Sin configuración

### ✅ Después (ListViewConfig)

```php
public function getListViewConfig(): ListViewConfig
{
    $config = new ListViewConfig();
    
    $config->columns([
        'id' => ['label' => 'ID', 'sortable' => true],
        'name' => ['label' => 'Nombre', 'searchable' => true],
        'status' => ['label' => 'Estado', 'format' => 'badge'],
        'created_at' => ['label' => 'Creado', 'format' => 'datetime'],
    ]);
    
    $config->addAction('Editar', 'users.edit', [
        'icon' => 'bi-pencil',
        'route_params' => ['id' => 'id'],
    ]);
    
    $config->search('Buscar...');
    $config->pagination(15);
    
    return $config;
}
```

**Ventajas:**
- ✅ Configuración completa
- ✅ Columnas con formato
- ✅ Acciones configurables
- ✅ Filtros integrados
- ✅ Búsqueda y paginación
- ✅ Extensible y mantenible

## 🎯 Beneficios

### ✅ Robustez
- Configuración tipada con objetos
- Validación automática
- Menos errores

### ✅ Flexibilidad
- Fácil agregar columnas, acciones, filtros
- Formatos personalizables
- Condicionales simples

### ✅ Mantenibilidad
- Configuración centralizada
- Fácil de modificar
- Código autodocumentado

### ✅ Reutilización
- Misma estructura para todos los listados
- Componentes reutilizables
- DRY (Don't Repeat Yourself)

---

**✅ Patrón implementado correctamente con objetos de configuración robustos**

**Patrones aplicados**:
- ✅ Builder Pattern (ListViewConfig)
- ✅ Configuration Object Pattern
- ✅ Value Object Pattern (ListColumn, ListAction, ListFilter)
- ✅ Fluent Interface
