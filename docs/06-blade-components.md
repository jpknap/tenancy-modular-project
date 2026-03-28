# 🎨 Blade Components Pattern - Documentación

## 📋 Componentes de Formulario Reutilizables

Se han creado componentes Blade reutilizables para eliminar código repetitivo en formularios.

## 🏗️ Arquitectura

```
resources/views/components/form/
├── input.blade.php      # Input text, email, password, etc.
├── textarea.blade.php   # Textarea
├── select.blade.php     # Select dropdown
├── checkbox.blade.php   # Checkbox
├── hidden.blade.php     # Hidden input
└── field.blade.php      # Renderizador dinámico (usa todos los anteriores)
```

## 📝 Componentes Disponibles

### 1. **Input** - Campos de texto

```blade
<x-form.input
    name="email"
    label="Email"
    type="email"
    placeholder="correo@ejemplo.com"
    :required="true"
/>
```

**Props disponibles:**
- `name` (required): Nombre del campo
- `label` (required): Etiqueta del campo
- `type` (default: 'text'): Tipo de input (text, email, password, number, etc.)
- `placeholder` (default: ''): Placeholder
- `required` (default: false): Si es requerido
- `value` (default: null): Valor por defecto

**Tipos soportados:**
- `text` - Texto simple
- `email` - Email
- `password` - Contraseña
- `number` - Número
- `date` - Fecha
- `tel` - Teléfono
- `url` - URL

### 2. **Textarea** - Área de texto

```blade
<x-form.textarea
    name="description"
    label="Descripción"
    placeholder="Ingrese una descripción"
    :rows="5"
    :required="true"
/>
```

**Props disponibles:**
- `name` (required): Nombre del campo
- `label` (required): Etiqueta del campo
- `placeholder` (default: ''): Placeholder
- `required` (default: false): Si es requerido
- `rows` (default: 3): Número de filas
- `value` (default: null): Valor por defecto

### 3. **Select** - Dropdown

```blade
<x-form.select
    name="status"
    label="Estado"
    :options="['active' => 'Activo', 'inactive' => 'Inactivo']"
    :required="true"
/>
```

**Props disponibles:**
- `name` (required): Nombre del campo
- `label` (required): Etiqueta del campo
- `options` (required): Array de opciones [value => label]
- `required` (default: false): Si es requerido
- `placeholder` (default: 'Seleccione una opción'): Texto del placeholder
- `value` (default: null): Valor seleccionado por defecto

### 4. **Checkbox** - Casilla de verificación

```blade
<x-form.checkbox
    name="accept_terms"
    label="Acepto los términos y condiciones"
    :checked="false"
/>
```

**Props disponibles:**
- `name` (required): Nombre del campo
- `label` (required): Etiqueta del campo
- `checked` (default: false): Si está marcado por defecto
- `value` (default: '1'): Valor cuando está marcado

### 5. **Hidden** - Campo oculto

```blade
<x-form.hidden
    name="user_id"
    :value="$user->id"
/>
```

**Props disponibles:**
- `name` (required): Nombre del campo
- `value` (default: null): Valor del campo

### 6. **Field** - Renderizador Dinámico ⭐

Este componente renderiza automáticamente el tipo correcto basándose en la configuración del FormBuilder.

```blade
<x-form.field :field="$field" />
```

**Props disponibles:**
- `field` (required): Array con la configuración del campo

**Estructura del array `$field`:**
```php
[
    'type' => 'text',           // Tipo de campo
    'name' => 'email',          // Nombre
    'label' => 'Email',         // Etiqueta
    'value' => 'test@test.com', // Valor por defecto
    'options' => [              // Opciones adicionales
        'placeholder' => 'Ingrese email',
        'required' => true,
        'rows' => 5,            // Solo para textarea
    ],
    'choices' => [              // Solo para select
        'active' => 'Activo',
        'inactive' => 'Inactivo',
    ],
]
```

## 🚀 Uso en Vistas

### Antes (Código Repetitivo) ❌

```blade
{{-- 90+ líneas de código repetitivo --}}
@foreach($form->getFields() as $field)
    @if($field['type'] === 'hidden')
        <input type="hidden" name="{{ $field['name'] }}" value="{{ $field['value'] ?? '' }}">
    
    @elseif($field['type'] === 'textarea')
        <div class="mb-3">
            <label for="{{ $field['name'] }}" class="form-label">
                {{ $field['label'] }}
                @if($field['options']['required'] ?? false)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <textarea 
                class="form-control @error($field['name']) is-invalid @enderror" 
                id="{{ $field['name'] }}" 
                name="{{ $field['name'] }}"
                rows="{{ $field['options']['rows'] ?? 3 }}"
                placeholder="{{ $field['options']['placeholder'] ?? '' }}"
                {{ ($field['options']['required'] ?? false) ? 'required' : '' }}
            >{{ old($field['name']) }}</textarea>
            @error($field['name'])
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    
    @elseif($field['type'] === 'select')
        {{-- Más código repetitivo... --}}
    @endif
@endforeach
```

### Después (Código Limpio) ✅

```blade
{{-- 3 líneas limpias y reutilizables --}}
@foreach($form->getFields() as $field)
    <x-form.field :field="$field" />
@endforeach
```

**Reducción**: De **90+ líneas** a **3 líneas** (97% menos código)

## 📚 Ejemplos de Uso

### Ejemplo 1: Formulario Manual

```blade
<form method="POST" action="/users">
    @csrf
    
    <x-form.input
        name="name"
        label="Nombre Completo"
        placeholder="Ingrese su nombre"
        :required="true"
    />
    
    <x-form.input
        name="email"
        label="Email"
        type="email"
        placeholder="correo@ejemplo.com"
        :required="true"
    />
    
    <x-form.input
        name="password"
        label="Contraseña"
        type="password"
        :required="true"
    />
    
    <x-form.select
        name="role"
        label="Rol"
        :options="['admin' => 'Administrador', 'user' => 'Usuario']"
        :required="true"
    />
    
    <x-form.checkbox
        name="accept_terms"
        label="Acepto los términos y condiciones"
    />
    
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
```

### Ejemplo 2: Con FormBuilder (Dinámico)

```blade
<form method="{{ $form->getMethod() }}" action="{{ $form->getAction() }}">
    @csrf
    
    @foreach($form->getFields() as $field)
        <x-form.field :field="$field" />
    @endforeach
    
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
```

### Ejemplo 3: Componentes Individuales con Atributos Extra

```blade
{{-- Agregar clases CSS adicionales --}}
<x-form.input
    name="phone"
    label="Teléfono"
    type="tel"
    class="custom-input"
/>

{{-- Agregar atributos HTML --}}
<x-form.input
    name="age"
    label="Edad"
    type="number"
    min="18"
    max="100"
/>

{{-- Deshabilitar campo --}}
<x-form.input
    name="username"
    label="Usuario"
    :value="$user->username"
    disabled
/>
```

## ✨ Características

### 1. **Validación Automática**
Todos los componentes incluyen soporte automático para errores de validación de Laravel:

```blade
<x-form.input name="email" label="Email" />
```

Si hay un error de validación para `email`, automáticamente:
- Agrega la clase `is-invalid` al input
- Muestra el mensaje de error debajo del campo
- Mantiene el valor anterior con `old()`

### 2. **Old Input Automático**
Los componentes recuerdan automáticamente los valores anteriores después de un error de validación:

```php
// Controller
return redirect()->back()->withErrors($validator)->withInput();
```

```blade
{{-- El componente automáticamente usa old('email') --}}
<x-form.input name="email" label="Email" />
```

### 3. **Campos Requeridos**
Marca visual automática para campos requeridos:

```blade
<x-form.input name="email" label="Email" :required="true" />
{{-- Muestra: Email * (con asterisco rojo) --}}
```

### 4. **Atributos Personalizados**
Puedes agregar cualquier atributo HTML adicional:

```blade
<x-form.input
    name="email"
    label="Email"
    class="custom-class"
    data-validation="email"
    autocomplete="off"
/>
```

## 🎯 Ventajas

### ✅ Código Limpio
- **Reducción de código**: 97% menos líneas
- **Legibilidad**: Código autodocumentado
- **Mantenibilidad**: Cambios centralizados

### ✅ Reutilización
- **DRY**: Don't Repeat Yourself
- **Consistencia**: Mismo estilo en toda la app
- **Productividad**: Desarrollo más rápido

### ✅ Mantenimiento
- **Centralizado**: Un solo lugar para cambios
- **Testeable**: Fácil de probar
- **Escalable**: Fácil agregar nuevos tipos

### ✅ Bootstrap 5
- **Clases correctas**: form-control, form-label, etc.
- **Validación**: is-invalid, invalid-feedback
- **Responsive**: Mobile-first

## 🔧 Personalización

### Agregar Nuevo Tipo de Campo

```blade
{{-- resources/views/components/form/date-range.blade.php --}}
@props(['name', 'label', 'required' => false])

<div class="mb-3">
    <label class="form-label">{{ $label }}</label>
    <div class="row">
        <div class="col-md-6">
            <input type="date" name="{{ $name }}_start" class="form-control">
        </div>
        <div class="col-md-6">
            <input type="date" name="{{ $name }}_end" class="form-control">
        </div>
    </div>
</div>
```

**Uso:**
```blade
<x-form.date-range name="period" label="Período" />
```

### Modificar Estilos Globalmente

Edita el componente base:
```blade
{{-- resources/views/components/form/input.blade.php --}}
<input
    type="{{ $type }}"
    class="form-control custom-input @error($name) is-invalid @enderror"
    {{-- Agrega 'custom-input' a todos los inputs --}}
>
```

## 📊 Comparación

| Aspecto | Antes | Después |
|---------|-------|---------|
| **Líneas de código** | 90+ | 3 |
| **Mantenibilidad** | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Reutilización** | ❌ | ✅ |
| **Legibilidad** | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Tiempo desarrollo** | Lento | Rápido |
| **Consistencia** | Variable | Uniforme |

## 🔗 Integración con FormBuilder

Los componentes están diseñados para trabajar perfectamente con el FormBuilder:

```php
// TenantFormRequest.php
public function buildForm(): FormBuilder
{
    return $this->formBuilder
        ->text('name', 'Nombre', ['required' => true])
        ->email('email', 'Email', ['required' => true])
        ->select('status', 'Estado', [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
        ]);
}
```

```blade
{{-- Vista --}}
@foreach($form->getFields() as $field)
    <x-form.field :field="$field" />
@endforeach
```

## 📖 Referencias

- [Laravel Blade Components](https://laravel.com/docs/blade#components)
- [Bootstrap 5 Forms](https://getbootstrap.com/docs/5.3/forms/overview/)
- [Component Pattern](https://refactoring.guru/design-patterns/composite)

---

**✅ Patrón implementado correctamente con Blade Components**

**Beneficios**:
- ✅ 97% menos código
- ✅ Totalmente reutilizable
- ✅ Fácil de mantener
- ✅ Consistente en toda la app
- ✅ Compatible con FormBuilder
