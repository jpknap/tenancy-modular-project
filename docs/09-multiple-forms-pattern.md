# 📋 Multiple Forms Pattern

Sistema de formularios múltiples con contextos/alias para diferentes casos de uso (create, edit, custom).

## 📑 Índice

- [Introducción](#introducción)
- [Arquitectura](#arquitectura)
- [BaseFormRequest](#baseformrequest)
- [Ejemplos](#ejemplos)
- [Uso en Adapters](#uso-en-adapters)
- [Ventajas](#ventajas)

## 🎯 Introducción

El patrón **Multiple Forms** permite definir **diferentes formularios** en un mismo FormRequest, usando contextos/alias:

- **`create`** - Formulario de creación
- **`edit`** - Formulario de edición
- **Alias personalizados** - `password-reset`, `profile`, etc.

### Problema que Resuelve

**❌ Antes:**
```php
// Un solo buildForm() para todo
// Password siempre requerido (problema en edit)
public function buildForm(): FormBuilder
{
    return $this->formBuilder
        ->password('password', 'Password', ['required' => true]); // ❌ Requerido en edit
}
```

**✅ Ahora:**
```php
// Formularios separados
public function buildCreateForm(): FormBuilder
{
    return $this->formBuilder
        ->password('password', 'Password', ['required' => true]); // ✅ Requerido
}

public function buildEditForm(): FormBuilder
{
    return $this->formBuilder
        ->password('password', 'Nueva Password (opcional)', ['required' => false]); // ✅ Opcional
}
```

## 🏗️ Arquitectura

```
app/Common/Admin/Form/
├── FormContext.php            # Enum con contextos disponibles
└── BaseFormRequest.php        # Define métodos: buildCreateForm(), buildEditForm(), buildCustomForm()

app/Projects/{Project}/FormRequests/
└── UserFormRequest.php         # Implementa los 3 métodos

Uso:
$formRequest->getFormBuilder(FormContext::CREATE)          → buildCreateForm()
$formRequest->getFormBuilder(FormContext::EDIT)            → buildEditForm()
$formRequest->getFormBuilder(FormContext::PASSWORD_RESET)  → buildCustomForm(FormContext::PASSWORD_RESET)
```

## 🎯 FormContext Enum

```php
namespace App\Common\Admin\Form;

enum FormContext: string
{
    case CREATE = 'create';
    case EDIT = 'edit';
    case PASSWORD_RESET = 'password-reset';
    case PROFILE = 'profile';
    case SETTINGS = 'settings';
    case PERMISSIONS = 'permissions';
    case BULK_IMPORT = 'bulk-import';

    // Helpers
    public function label(): string;
    public function isCreate(): bool;
    public function isEdit(): bool;
    public function isCustom(): bool;
}
```

### Ventajas del Enum

✅ **Type Safety** - Errores en compile-time, no runtime  
✅ **Autocompletado** - IDE sugiere valores disponibles  
✅ **Refactoring seguro** - Cambios automáticos en toda la app  
✅ **Sin typos** - Imposible escribir 'crete' en lugar de 'create'  
✅ **Documentación integrada** - Los casos están auto-documentados

## 📝 BaseFormRequest

### Estructura

```php
namespace App\Common\Admin\Form;

abstract class BaseFormRequest extends FormRequest
{
    protected ?FormBuilder $formBuilder = null;
    protected FormContext $currentContext = FormContext::CREATE;

    /**
     * Define formulario de creación
     */
    abstract public function buildCreateForm(): FormBuilder;

    /**
     * Define formulario de edición
     */
    abstract public function buildEditForm(): FormBuilder;

    /**
     * Define formularios personalizados (opcional)
     */
    public function buildCustomForm(FormContext $context): FormBuilder
    {
        throw new \BadMethodCallException("Custom form '{$context->value}' not implemented");
    }

    /**
     * Obtiene FormBuilder según contexto
     */
    public function getFormBuilder(FormContext $context = FormContext::CREATE): FormBuilder
    {
        if ($this->formBuilder === null) {
            $this->formBuilder = new FormBuilder();
        }

        $this->currentContext = $context;

        return match ($context) {
            FormContext::CREATE => $this->buildCreateForm(),
            FormContext::EDIT => $this->buildEditForm(),
            default => $this->buildCustomForm($context),
        };
    }

    // Helpers
    public function getContext(): FormContext { ... }
    public function isCreating(): bool { ... }
    public function isUpdating(): bool { ... }
    public function getModelId(): ?int { ... }
}
```

### Métodos Disponibles

| Método | Descripción | Retorno |
|--------|-------------|---------|
| `getFormBuilder(FormContext $context)` | Obtiene FormBuilder por contexto | `FormBuilder` |
| `getContext()` | Obtiene contexto actual | `FormContext` |
| `isCreating()` | Detecta si es creación (no hay ID) | `bool` |
| `isUpdating()` | Detecta si es edición (hay ID) | `bool` |
| `getModelId()` | ID del modelo (si existe) | `?int` |

## 💡 Ejemplos

### Ejemplo 1: UserFormRequest Completo

```php
namespace App\Projects\Landlord\FormRequests;

use App\Common\Admin\Form\BaseFormRequest;
use App\Common\Admin\Form\FormBuilder;
use App\Common\Admin\Form\FormContext;

class UserFormRequest extends BaseFormRequest
{
    /**
     * Formulario de CREACIÓN
     * Password requerido, checkbox activo por defecto
     */
    public function buildCreateForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('POST')
            ->text('name', 'Nombre Completo', [
                'required' => true,
            ])
            ->email('email', 'Email', [
                'required' => true,
            ])
            ->password('password', 'Contraseña', [
                'required' => true,
                'placeholder' => 'Mínimo 8 caracteres',
            ])
            ->password('password_confirmation', 'Confirmar Contraseña', [
                'required' => true,
            ])
            ->select('role', 'Rol', [
                'admin' => 'Administrador',
                'user' => 'Usuario',
            ], ['required' => true])
            ->checkbox('is_active', 'Usuario Activo', [
                'checked' => true,
            ]);
    }

    /**
     * Formulario de EDICIÓN
     * Password opcional, sin checkbox pre-marcado
     */
    public function buildEditForm(): FormBuilder
    {
        return $this->formBuilder
            ->setMethod('PUT')
            ->text('name', 'Nombre Completo', [
                'required' => true,
            ])
            ->email('email', 'Email', [
                'required' => true,
            ])
            ->password('password', 'Nueva Contraseña (opcional)', [
                'required' => false,
                'placeholder' => 'Dejar en blanco para mantener actual',
            ])
            ->password('password_confirmation', 'Confirmar Contraseña', [
                'required' => false,
            ])
            ->select('role', 'Rol', [
                'admin' => 'Administrador',
                'user' => 'Usuario',
            ], ['required' => true])
            ->checkbox('is_active', 'Usuario Activo');
    }

    /**
     * Formularios PERSONALIZADOS
     */
    public function buildCustomForm(FormContext $context): FormBuilder
    {
        return match ($context) {
            // Formulario de cambio de contraseña
            FormContext::PASSWORD_RESET => $this->formBuilder
                ->password('current_password', 'Contraseña Actual', [
                    'required' => true,
                ])
                ->password('password', 'Nueva Contraseña', [
                    'required' => true,
                    'placeholder' => 'Mínimo 8 caracteres',
                ])
                ->password('password_confirmation', 'Confirmar Contraseña', [
                    'required' => true,
                ]),
            
            // Formulario de perfil (sin password)
            FormContext::PROFILE => $this->formBuilder
                ->text('name', 'Nombre Completo', [
                    'required' => true,
                ])
                ->email('email', 'Email', [
                    'required' => true,
                    'readonly' => true,
                ]),

            default => parent::buildCustomForm($context),
        };
    }

    /**
     * Reglas de validación dinámicas
     */
    public function rules(): array
    {
        $userId = $this->getModelId();

        // Reglas base
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'role' => ['required', 'in:admin,user'],
            'is_active' => ['nullable', 'boolean'],
        ];

        // Password según contexto
        if ($this->isCreating()) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        // Reglas para password-reset
        if ($this->getContext() === FormContext::PASSWORD_RESET) {
            $rules = [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ];
        }

        return $rules;
    }
}
```

### Ejemplo 2: TenantFormRequest Simple

```php
class TenantFormRequest extends BaseFormRequest
{
    /**
     * Formulario de creación
     */
    public function buildCreateForm(): FormBuilder
    {
        return $this->formBuilder
            ->text('name', 'Nombre del Tenant')
            ->email('email', 'Email')
            ->select('status', 'Estado', [
                'active' => 'Activo',
                'pending' => 'Pendiente',
                'inactive' => 'Inactivo',
            ]);
    }

    /**
     * Formulario de edición (igual al create)
     */
    public function buildEditForm(): FormBuilder
    {
        return $this->buildCreateForm(); // Reutiliza el mismo
    }

    public function rules(): array
    {
        $tenantId = $this->getModelId();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:tenants,email,' . $tenantId],
            'status' => ['required', 'in:active,pending,inactive'],
        ];
    }
}
```

## 🎯 Uso en Adapters

Los adapters usan el contexto correcto automáticamente:

```php
abstract class AdminBaseAdapter
{
    public function getCreateViewConfig(): CreateViewConfig
    {
        $formRequestClass = $this->getFormRequest();
        $formRequest = new $formRequestClass();

        // Contexto CREATE
        $config = new CreateViewConfig($formRequest->getFormBuilder(FormContext::CREATE));

        return $config;
    }

    public function getEditViewConfig(mixed $item): EditViewConfig
    {
        $formRequestClass = $this->getFormRequest();
        $formRequest = new $formRequestClass();

        // Contexto EDIT
        $config = new EditViewConfig($formRequest->getFormBuilder(FormContext::EDIT));

        $config->item($item);

        return $config;
    }
}
```

## 🔄 Flujo Completo

### Creación

```
Controller → getCreateViewConfig()
    ↓
Adapter → getFormBuilder(FormContext::CREATE)
    ↓
FormRequest → buildCreateForm()
    ↓
CreateViewConfig → view('create')
    ↓
Formulario con password requerido
```

### Edición

```
Controller → getEditViewConfig($item)
    ↓
Adapter → getFormBuilder(FormContext::EDIT)
    ↓
FormRequest → buildEditForm()
    ↓
EditViewConfig → view('edit')
    ↓
Formulario con password opcional
```

### Custom (ej: password-reset)

```
Controller → custom action
    ↓
FormRequest → getFormBuilder(FormContext::PASSWORD_RESET)
    ↓
buildCustomForm(FormContext::PASSWORD_RESET)
    ↓
Formulario solo de passwords
```

## ✨ Ventajas

### ✅ Separación de Responsabilidades
- Formularios diferentes para diferentes contextos
- Sin lógica condicional compleja en buildForm()

### ✅ Flexibilidad
- Fácil agregar nuevos formularios con alias
- Reutilización de formularios cuando sean iguales

### ✅ Validaciones Contextuales
- Reglas diferentes por contexto
- `isCreating()`, `isUpdating()`, `getContext()`

### ✅ Type Safety
- Métodos abstractos forzados
- Error claro si falta implementar algún formulario

### ✅ Escalabilidad
- Agregar nuevos contextos sin modificar base
- Match expression para casos personalizados

## 📊 Comparación

| Aspecto | Un Solo FormBuilder | Multiple Forms |
|---------|---------------------|----------------|
| **Contextos** | 1 formulario para todo | Create, Edit, Custom |
| **Password** | Siempre requerido/opcional | Requerido en create, opcional en edit |
| **Validaciones** | Complejas con ifs | Contextuales y claras |
| **Mantenibilidad** | Baja (mucha lógica) | Alta (separado por contexto) |
| **Extensibilidad** | Difícil | Fácil (solo agregar método) |

## 🎯 Casos de Uso

### Formulario Create vs Edit

**Create:**
- Password requerido
- Email único global
- Checkbox "activo" marcado por defecto
- Todos los campos visibles

**Edit:**
- Password opcional (solo si se quiere cambiar)
- Email único excepto el actual
- Checkbox sin pre-marcar
- Algunos campos readonly

### Formularios Personalizados

```php
FormContext::PASSWORD_RESET  → Solo campos de password
FormContext::PROFILE         → Solo nombre y email (sin password)
FormContext::PERMISSIONS     → Solo checkboxes de permisos
FormContext::SETTINGS        → Solo configuraciones
FormContext::BULK_IMPORT     → Solo file upload
```

## 📖 Ver También

- [Form Builder Pattern](./04-form-builder-pattern.md) - Constructor de formularios
- [Admin View Config Pattern](./08-admin-view-config-pattern.md) - Configuración de vistas
- [Repository Pattern](./02-repository-pattern.md) - Acceso a datos

---

**Última actualización**: Octubre 2025
**Patrón**: Template Method + Strategy + Context
