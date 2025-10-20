# 📚 Ejemplos de Blade Components

## Ejemplos Prácticos de Uso

### Ejemplo 1: Formulario de Registro de Usuario

```blade
{{-- resources/views/users/create.blade.php --}}
@extends('layouts.layout_menu_sidebar')

@section('content')
    <div class="content-card">
        <h2>Nuevo Usuario</h2>
        
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            
            <x-form.input
                name="name"
                label="Nombre Completo"
                placeholder="Juan Pérez"
                :required="true"
            />
            
            <x-form.input
                name="email"
                label="Email"
                type="email"
                placeholder="juan@ejemplo.com"
                :required="true"
            />
            
            <x-form.input
                name="password"
                label="Contraseña"
                type="password"
                :required="true"
            />
            
            <x-form.input
                name="password_confirmation"
                label="Confirmar Contraseña"
                type="password"
                :required="true"
            />
            
            <x-form.select
                name="role"
                label="Rol"
                :options="[
                    'admin' => 'Administrador',
                    'user' => 'Usuario',
                    'guest' => 'Invitado'
                ]"
                :required="true"
            />
            
            <x-form.checkbox
                name="is_active"
                label="Usuario Activo"
                :checked="true"
            />
            
            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </form>
    </div>
@endsection
```

### Ejemplo 2: Formulario de Edición con Valores

```blade
{{-- resources/views/users/edit.blade.php --}}
@extends('layouts.layout_menu_sidebar')

@section('content')
    <div class="content-card">
        <h2>Editar Usuario: {{ $user->name }}</h2>
        
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            
            <x-form.hidden name="id" :value="$user->id" />
            
            <x-form.input
                name="name"
                label="Nombre Completo"
                :value="$user->name"
                :required="true"
            />
            
            <x-form.input
                name="email"
                label="Email"
                type="email"
                :value="$user->email"
                :required="true"
            />
            
            <x-form.select
                name="role"
                label="Rol"
                :options="[
                    'admin' => 'Administrador',
                    'user' => 'Usuario',
                    'guest' => 'Invitado'
                ]"
                :value="$user->role"
                :required="true"
            />
            
            <x-form.checkbox
                name="is_active"
                label="Usuario Activo"
                :checked="$user->is_active"
            />
            
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
@endsection
```

### Ejemplo 3: Formulario de Búsqueda

```blade
{{-- resources/views/users/index.blade.php --}}
@extends('layouts.layout_menu_sidebar')

@section('content')
    <div class="content-card mb-3">
        <h3>Filtros de Búsqueda</h3>
        
        <form method="GET" action="{{ route('users.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <x-form.input
                        name="search"
                        label="Buscar"
                        placeholder="Nombre o email..."
                        :value="request('search')"
                    />
                </div>
                
                <div class="col-md-3">
                    <x-form.select
                        name="role"
                        label="Rol"
                        :options="[
                            '' => 'Todos',
                            'admin' => 'Administrador',
                            'user' => 'Usuario',
                            'guest' => 'Invitado'
                        ]"
                        :value="request('role')"
                    />
                </div>
                
                <div class="col-md-3">
                    <x-form.select
                        name="status"
                        label="Estado"
                        :options="[
                            '' => 'Todos',
                            'active' => 'Activos',
                            'inactive' => 'Inactivos'
                        ]"
                        :value="request('status')"
                    />
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </div>
            </div>
        </form>
    </div>
    
    {{-- Tabla de resultados --}}
    <div class="content-card">
        {{-- ... tabla ... --}}
    </div>
@endsection
```

### Ejemplo 4: Formulario con Atributos Personalizados

```blade
<form method="POST" action="{{ route('products.store') }}">
    @csrf
    
    {{-- Input con autocompletado deshabilitado --}}
    <x-form.input
        name="sku"
        label="SKU"
        autocomplete="off"
        :required="true"
    />
    
    {{-- Input numérico con min/max --}}
    <x-form.input
        name="price"
        label="Precio"
        type="number"
        step="0.01"
        min="0"
        max="99999.99"
        :required="true"
    />
    
    {{-- Input con pattern de validación --}}
    <x-form.input
        name="phone"
        label="Teléfono"
        type="tel"
        pattern="[0-9]{10}"
        placeholder="1234567890"
    />
    
    {{-- Textarea con contador de caracteres --}}
    <x-form.textarea
        name="description"
        label="Descripción"
        :rows="5"
        maxlength="500"
        :required="true"
    />
    
    {{-- Select con data attributes --}}
    <x-form.select
        name="category_id"
        label="Categoría"
        :options="$categories"
        data-live-search="true"
        :required="true"
    />
    
    <button type="submit" class="btn btn-primary">Guardar Producto</button>
</form>
```

### Ejemplo 5: Formulario Dinámico con FormBuilder

```php
// TenantFormRequest.php
public function buildForm(): FormBuilder
{
    return $this->formBuilder
        ->setMethod('POST')
        ->setAction(route('tenants.store'))
        ->text('name', 'Nombre del Tenant', [
            'placeholder' => 'Ingrese el nombre',
            'required' => true,
        ])
        ->email('email', 'Email', [
            'placeholder' => 'correo@ejemplo.com',
            'required' => true,
        ])
        ->select('status', 'Estado', [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ], ['required' => true])
        ->textarea('description', 'Descripción', [
            'rows' => 4,
            'placeholder' => 'Descripción opcional',
        ])
        ->checkbox('send_welcome_email', 'Enviar email de bienvenida', [
            'checked' => true,
        ]);
}
```

```blade
{{-- Vista --}}
<form method="{{ $form->getMethod() }}" action="{{ $form->getAction() }}">
    @csrf
    
    @foreach($form->getFields() as $field)
        <x-form.field :field="$field" />
    @endforeach
    
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
```

### Ejemplo 6: Formulario Multi-Step

```blade
{{-- Step 1: Información Personal --}}
<div id="step-1" class="form-step">
    <h3>Paso 1: Información Personal</h3>
    
    <x-form.input
        name="first_name"
        label="Nombre"
        :required="true"
    />
    
    <x-form.input
        name="last_name"
        label="Apellido"
        :required="true"
    />
    
    <x-form.input
        name="email"
        label="Email"
        type="email"
        :required="true"
    />
    
    <button type="button" class="btn btn-primary" onclick="nextStep()">Siguiente</button>
</div>

{{-- Step 2: Información de Empresa --}}
<div id="step-2" class="form-step d-none">
    <h3>Paso 2: Información de Empresa</h3>
    
    <x-form.input
        name="company_name"
        label="Nombre de la Empresa"
        :required="true"
    />
    
    <x-form.select
        name="industry"
        label="Industria"
        :options="$industries"
        :required="true"
    />
    
    <x-form.textarea
        name="company_description"
        label="Descripción de la Empresa"
        :rows="4"
    />
    
    <button type="button" class="btn btn-secondary" onclick="prevStep()">Anterior</button>
    <button type="submit" class="btn btn-primary">Finalizar</button>
</div>
```

### Ejemplo 7: Formulario con Validación en Tiempo Real

```blade
<form method="POST" action="{{ route('users.store') }}" id="user-form">
    @csrf
    
    <x-form.input
        name="username"
        label="Nombre de Usuario"
        :required="true"
        data-validate="username"
        data-min-length="3"
    />
    <small class="text-muted">Mínimo 3 caracteres, solo letras y números</small>
    
    <x-form.input
        name="email"
        label="Email"
        type="email"
        :required="true"
        data-validate="email"
    />
    <small class="text-muted">Verificaremos si el email está disponible</small>
    
    <x-form.input
        name="password"
        label="Contraseña"
        type="password"
        :required="true"
        data-validate="password"
        data-min-length="8"
    />
    <div class="password-strength"></div>
    
    <button type="submit" class="btn btn-primary">Crear Usuario</button>
</form>

<script>
// Validación en tiempo real
document.querySelectorAll('[data-validate]').forEach(input => {
    input.addEventListener('blur', function() {
        validateField(this);
    });
});
</script>
```

## 🎨 Estilos Personalizados

### Agregar Clases CSS Personalizadas

```blade
{{-- Input con clase personalizada --}}
<x-form.input
    name="email"
    label="Email"
    class="custom-input-class"
/>

{{-- Select con múltiples clases --}}
<x-form.select
    name="category"
    label="Categoría"
    :options="$categories"
    class="select2 custom-select"
/>
```

### Wrapper Personalizado

```blade
{{-- Agregar un wrapper div --}}
<div class="col-md-6">
    <x-form.input
        name="first_name"
        label="Nombre"
        :required="true"
    />
</div>

<div class="col-md-6">
    <x-form.input
        name="last_name"
        label="Apellido"
        :required="true"
    />
</div>
```

## 🔧 Tips y Trucos

### 1. Valores Dinámicos desde Controller

```php
// Controller
public function create()
{
    $roles = [
        'admin' => 'Administrador',
        'user' => 'Usuario',
        'guest' => 'Invitado',
    ];
    
    return view('users.create', compact('roles'));
}
```

```blade
{{-- Vista --}}
<x-form.select
    name="role"
    label="Rol"
    :options="$roles"
    :required="true"
/>
```

### 2. Valores desde Base de Datos

```php
// Controller
public function create()
{
    $categories = Category::pluck('name', 'id')->toArray();
    return view('products.create', compact('categories'));
}
```

```blade
<x-form.select
    name="category_id"
    label="Categoría"
    :options="$categories"
    :required="true"
/>
```

### 3. Campos Condicionales

```blade
@if(auth()->user()->isAdmin())
    <x-form.select
        name="role"
        label="Rol"
        :options="$roles"
        :required="true"
    />
@endif
```

### 4. Campos Deshabilitados

```blade
<x-form.input
    name="username"
    label="Usuario"
    :value="$user->username"
    disabled
    readonly
/>
```

---

**✅ Componentes listos para usar en cualquier formulario**
