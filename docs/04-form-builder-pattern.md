# 🏗️ Form Builder Pattern - Documentación

## 📋 Arquitectura Implementada

```
app/Common/Form/
├── FormBuilder.php          # Builder Pattern - Construcción fluida
├── BaseFormRequest.php      # Template Method Pattern - Base validación

app/Projects/Landlord/Requests/
├── TenantFormRequest.php    # Implementación específica Tenant
└── UserFormRequest.php      # Implementación específica User
```

## 🎯 Patrones de Diseño Utilizados

### 1. **Builder Pattern** (FormBuilder)

**Propósito**: Construir objetos complejos paso a paso con sintaxis fluida.

**Implementación**:
```php
$form = new FormBuilder();
$form->text('name', 'Nombre')
     ->email('email', 'Email')
     ->textarea('description', 'Descripción')
     ->date('start_date', 'Fecha');
```

**Ventajas**:
- ✅ Sintaxis legible y expresiva
- ✅ Construcción paso a paso
- ✅ Método encadenado (fluent interface)
- ✅ Separación de construcción y representación

**Código**:
```php
class FormBuilder
{
    private array $fields = [];
    
    public function text(string $name, string $label, array $options = []): self
    {
        $this->fields[] = ['type' => 'text', 'name' => $name, 'label' => $label];
        return $this; // Retorna $this para encadenar
    }
    
    public function getFields(): array
    {
        return $this->fields;
    }
}
```

### 2. **Template Method Pattern** (BaseFormRequest)

**Propósito**: Define el esqueleto de un algoritmo, delegando pasos específicos a subclases.

**Implementación**:
```php
abstract class BaseFormRequest extends FormRequest
{
    // Template method - Define el flujo
    public function getFormBuilder(): FormBuilder
    {
        return $this->buildForm(); // Llama al hook method
    }
    
    // Hook methods - Implementados por subclases
    abstract public function buildForm(): FormBuilder;
    abstract public function rules(): array;
}
```

**Ventajas**:
- ✅ Reutilización de código común
- ✅ Extensibilidad controlada
- ✅ Consistencia en el flujo
- ✅ Inversión de control

### 3. **Strategy Pattern** (Validación)

**Propósito**: Encapsular diferentes estrategias de validación intercambiables.

**Implementación**:
```php
// Cada FormRequest define su propia estrategia
class TenantFormRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
        ];
    }
}
```

### 4. **Adapter Pattern** (AdminBaseAdapter)

**Propósito**: Adaptar la interfaz del FormRequest al contexto del AdminController.

**Implementación**:
```php
abstract class AdminBaseAdapter
{
    abstract public function getFormRequest(): string;
    
    // Adapta el FormRequest al flujo del Admin
}
```

## ✅ Principios SOLID Aplicados

### **S - Single Responsibility Principle**

Cada clase tiene una única responsabilidad bien definida:

| Clase | Responsabilidad Única |
|-------|----------------------|
| `FormBuilder` | Construir estructura de formularios |
| `BaseFormRequest` | Validación y construcción de formularios |
| `TenantFormRequest` | Reglas específicas de Tenant |
| `AdminController` | Manejo de rutas CRUD |
| `AdminBaseAdapter` | Configuración del módulo Admin |

**Ejemplo**:
```php
// ✅ CORRECTO - Una responsabilidad
class FormBuilder {
    public function text() { /* solo construye campos */ }
}

// ❌ INCORRECTO - Múltiples responsabilidades
class FormBuilder {
    public function text() { }
    public function validate() { } // Validación es otra responsabilidad
    public function save() { }     // Persistencia es otra responsabilidad
}
```

### **O - Open/Closed Principle**

Abierto para extensión, cerrado para modificación:

```php
// ✅ Extensión sin modificar BaseFormRequest
class TenantFormRequest extends BaseFormRequest
{
    public function buildForm(): FormBuilder
    {
        // Extiende funcionalidad sin tocar la clase base
        return $this->formBuilder->text('name', 'Nombre');
    }
}

// ✅ Agregar nuevo FormRequest no requiere cambios en código existente
class ProductFormRequest extends BaseFormRequest { }
```

### **L - Liskov Substitution Principle**

Cualquier `BaseFormRequest` puede sustituirse por sus subclases:

```php
// ✅ Funciona con cualquier FormRequest
function renderForm(BaseFormRequest $request)
{
    $form = $request->getFormBuilder();
    // Funciona con TenantFormRequest, UserFormRequest, etc.
}

// Uso
renderForm(new TenantFormRequest());  // ✅ Funciona
renderForm(new UserFormRequest());    // ✅ Funciona
```

### **I - Interface Segregation Principle**

Interfaces pequeñas y específicas:

```php
// ✅ Interfaz específica y pequeña
interface FormBuilderInterface {
    public function text(string $name, string $label): self;
    public function email(string $name, string $label): self;
}

// ❌ Interfaz grande y monolítica
interface FormInterface {
    public function build();
    public function validate();
    public function save();
    public function render();
    // ... muchos más métodos
}
```

### **D - Dependency Inversion Principle**

Depende de abstracciones, no de implementaciones concretas:

```php
// ✅ Depende de abstracción (BaseFormRequest)
class AdminController {
    public function new() {
        $formRequestClass = $this->admin->getFormRequest();
        $formRequest = app($formRequestClass); // Abstracción
    }
}

// ❌ Depende de implementación concreta
class AdminController {
    public function new(TenantFormRequest $request) {
        // Acoplado a TenantFormRequest específicamente
    }
}
```

## 🚀 Flujo de Datos Completo

```
1. Usuario accede a /admin/tenant/new (GET)
    ↓
2. AdminController::new()
    ↓
3. AdminBaseAdapter::getFormRequest() → TenantFormRequest::class
    ↓
4. app(TenantFormRequest::class) → Instancia del FormRequest
    ↓
5. TenantFormRequest::buildForm() → Construye FormBuilder
    ↓
6. FormBuilder construye estructura de campos
    ↓
7. Vista renderiza formulario HTML
    ↓
8. Usuario completa y envía formulario (POST)
    ↓
9. Laravel inyecta TenantFormRequest automáticamente
    ↓
10. TenantFormRequest::rules() → Validación automática
    ↓
11. Si falla: Redirect back con errores
    Si pasa: AdminController::create()
    ↓
12. $validated = $formRequest->validated()
    ↓
13. Repository::create($validated)
    ↓
14. Redirect a lista con mensaje de éxito
```

## 📝 Uso Completo - Ejemplo Real

### 1. Crear FormRequest

```php
<?php

namespace App\Projects\Landlord\Requests;

use App\Common\Form\BaseFormRequest;
use App\Common\Form\FormBuilder;

class TenantFormRequest extends BaseFormRequest
{
    public function buildForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('POST')
            ->setAction(route('landlord.admin.tenant.create'))
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
            ])
            ->date('start_date', 'Fecha de Inicio', [
                'required' => true,
            ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:tenants,email'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'email.unique' => 'Este email ya está registrado',
            'start_date.after_or_equal' => 'La fecha debe ser hoy o posterior',
        ];
    }
}
```

### 2. Registrar en AdminAdapter

```php
class TenantAdmin extends AdminBaseAdapter
{
    public function getFormRequest(): string
    {
        return TenantFormRequest::class;
    }
}
```

### 3. Controlador (Ya implementado)

```php
#[Route('new', methods: ['GET'], name: 'new')]
public function new()
{
    $formRequestClass = $this->admin->getFormRequest();
    $formRequest = app($formRequestClass);
    
    return view('landlord.new', [
        'admin' => $this->admin,
        'form' => $formRequest->getFormBuilder(),
    ]);
}

#[Route('create', methods: ['POST'], name: 'create')]
public function create()
{
    $formRequestClass = $this->admin->getFormRequest();
    $validated = app($formRequestClass)->validated();
    
    $item = app($this->admin->repository())->create($validated);
    
    return redirect()
        ->route('landlord.admin.' . $this->admin->getRoutePrefix() . '.list')
        ->with('success', 'Registro creado exitosamente');
}
```

## 🎨 Tipos de Campos Disponibles

| Método | Tipo HTML | Ejemplo de Uso |
|--------|-----------|----------------|
| `text()` | `<input type="text">` | `->text('name', 'Nombre')` |
| `email()` | `<input type="email">` | `->email('email', 'Email')` |
| `password()` | `<input type="password">` | `->password('pass', 'Contraseña')` |
| `textarea()` | `<textarea>` | `->textarea('desc', 'Descripción')` |
| `select()` | `<select>` | `->select('status', 'Estado', $opts)` |
| `checkbox()` | `<input type="checkbox">` | `->checkbox('active', 'Activo')` |
| `date()` | `<input type="date">` | `->date('birth', 'Fecha')` |
| `number()` | `<input type="number">` | `->number('age', 'Edad')` |
| `hidden()` | `<input type="hidden">` | `->hidden('id', $value)` |

## 🎯 Ventajas de esta Arquitectura

### ✅ Mantenibilidad
- **Código organizado**: Cada componente en su lugar
- **Fácil de entender**: Patrones conocidos
- **Cambios localizados**: Modificar sin afectar otros módulos

### ✅ Escalabilidad
- **Fácil agregar formularios**: Solo crear nuevo FormRequest
- **No afecta código existente**: Open/Closed Principle
- **Aislado por proyecto**: Cada proyecto sus propios FormRequests

### ✅ Testabilidad
- **Componentes independientes**: Fácil de aislar
- **Fácil de mockear**: Interfaces claras
- **Tests unitarios simples**: Sin dependencias complejas

### ✅ Reutilización
- **FormBuilder reutilizable**: Usado por todos los FormRequests
- **BaseFormRequest compartido**: Lógica común centralizada
- **Patrones consistentes**: Misma estructura en todo el proyecto

### ✅ Type Safety
- **Todo tipado**: PHP 8.2+ type hints
- **IDE autocomplete**: Mejor experiencia de desarrollo
- **Menos errores**: Detección en tiempo de compilación

## 🧪 Testing

### Test del FormBuilder

```php
use Tests\TestCase;
use App\Common\Form\FormBuilder;

class FormBuilderTest extends TestCase
{
    public function test_can_build_text_field()
    {
        $builder = new FormBuilder();
        $builder->text('name', 'Nombre');
        
        $fields = $builder->getFields();
        
        $this->assertCount(1, $fields);
        $this->assertEquals('text', $fields[0]['type']);
        $this->assertEquals('name', $fields[0]['name']);
    }
    
    public function test_fluent_interface_works()
    {
        $builder = new FormBuilder();
        
        $result = $builder
            ->text('name', 'Nombre')
            ->email('email', 'Email');
        
        $this->assertInstanceOf(FormBuilder::class, $result);
        $this->assertCount(2, $builder->getFields());
    }
}
```

### Test del FormRequest

```php
use Tests\TestCase;
use App\Projects\Landlord\Requests\TenantFormRequest;
use Illuminate\Support\Facades\Validator;

class TenantFormRequestTest extends TestCase
{
    public function test_form_has_correct_fields()
    {
        $request = new TenantFormRequest();
        $form = $request->getFormBuilder();
        
        $fields = $form->getFields();
        
        $this->assertCount(5, $fields);
        $this->assertEquals('name', $fields[0]['name']);
    }
    
    public function test_validation_fails_with_invalid_email()
    {
        $request = new TenantFormRequest();
        
        $validator = Validator::make(
            ['email' => 'invalid-email'],
            $request->rules()
        );
        
        $this->assertTrue($validator->fails());
    }
    
    public function test_validation_passes_with_valid_data()
    {
        $request = new TenantFormRequest();
        
        $validator = Validator::make([
            'name' => 'Test Tenant',
            'email' => 'test@example.com',
            'status' => 'active',
            'start_date' => now()->addDay()->format('Y-m-d'),
        ], $request->rules());
        
        $this->assertFalse($validator->fails());
    }
}
```

## 📚 Comparación con Otros Enfoques

### ❌ Enfoque Tradicional (Sin Patrón)

```php
// Controlador con lógica mezclada
public function create(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
    ]);
    
    // Lógica de negocio mezclada
    $tenant = Tenant::create($request->all());
    
    return redirect()->back();
}
```

**Problemas**:
- ❌ Validación mezclada con lógica
- ❌ No reutilizable
- ❌ Difícil de testear
- ❌ No escalable

### ✅ Nuestro Enfoque (Con Patrones)

```php
// Separación de responsabilidades
public function create()
{
    $validated = app($this->admin->getFormRequest())->validated();
    $item = app($this->admin->repository())->create($validated);
    return redirect()->route('list')->with('success', 'Creado');
}
```

**Ventajas**:
- ✅ Separación clara de responsabilidades
- ✅ Reutilizable y extensible
- ✅ Fácil de testear
- ✅ Escalable y mantenible

## 🔮 Extensiones Futuras

### 1. Validación en Cliente (JavaScript)

```php
public function getClientValidation(): array
{
    return [
        'name' => ['required', 'minLength:3'],
        'email' => ['required', 'email'],
    ];
}
```

### 2. Campos Dependientes

```php
->select('country', 'País', $countries)
->select('state', 'Estado', [], [
    'depends_on' => 'country',
    'ajax_url' => route('api.states'),
]);
```

### 3. File Uploads

```php
->file('avatar', 'Avatar', [
    'accept' => 'image/*',
    'max_size' => '2MB',
]);
```

### 4. Campos Dinámicos (Repeater)

```php
->repeater('contacts', 'Contactos', [
    'text' => ['name', 'Nombre'],
    'email' => ['email', 'Email'],
]);
```

## 📖 Referencias

- [Builder Pattern - Gang of Four](https://refactoring.guru/design-patterns/builder)
- [Template Method Pattern](https://refactoring.guru/design-patterns/template-method)
- [Strategy Pattern](https://refactoring.guru/design-patterns/strategy)
- [Laravel Form Requests](https://laravel.com/docs/validation#form-request-validation)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)

---

**✅ Implementación completa, limpia y siguiendo SOLID**

**Patrones utilizados**: Builder, Template Method, Strategy, Adapter  
**Principios SOLID**: Todos aplicados correctamente  
**Arquitectura**: Limpia, escalable y mantenible
