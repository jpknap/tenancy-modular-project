# 📚 Documentación del Proyecto - Tenancy Modular

Bienvenido a la documentación del proyecto **Tenancy Modular**. Este proyecto implementa una arquitectura modular multi-tenant con patrones de diseño robustos.

## 📖 Índice de Documentación

### 🏗️ Arquitectura y Patrones

1. **[Resumen de Implementación](./01-implementation-summary.md)**
   - Visión general del proyecto
   - Arquitectura modular
   - Estructura de carpetas
   - Patrones implementados

2. **[Repository Pattern](./02-repository-pattern.md)**
   - Patrón de repositorio
   - Abstracción de acceso a datos
   - Ejemplos de uso

3. **[Service Layer Pattern](./03-service-layer-pattern.md)**
   - Capa de servicios
   - Lógica de negocio
   - Transacciones y validaciones

### 🎨 UI y Componentes

4. **[Form Builder Pattern](./04-form-builder-pattern.md)**
   - Constructor de formularios
   - Validaciones
   - Campos personalizados

5. **[ListView Config Pattern](./05-listview-config-pattern.md)**
   - Configuración de listados
   - Columnas, acciones y filtros
   - StatCards (tarjetas de estadísticas)

6. **[Blade Components](./06-blade-components.md)**
   - Componentes reutilizables
   - Ejemplos prácticos
   - Guía de uso

### 🎯 Guías de Diseño

7. **[Design Guide](./07-design-guide.md)**
   - Guía de diseño visual
   - Colores y tipografía
   - Componentes UI

## 🚀 Inicio Rápido

### Estructura del Proyecto

```
app/
├── Common/                    # Código compartido
│   ├── Admin/                # Sistema de administración
│   │   ├── Adapter/         # Adaptadores de admin
│   │   └── Controller/      # Controladores base
│   ├── FormBuilder/         # Constructor de formularios
│   ├── ListView/            # Sistema de listados
│   └── Repositories/        # Repositorios base
│
└── Projects/                 # Proyectos modulares
    └── Landlord/            # Proyecto principal
        ├── Adapters/        # Adaptadores específicos
        ├── Http/            # Controllers y Requests
        ├── Models/          # Modelos Eloquent
        ├── Repositories/    # Repositorios del proyecto
        └── Services/        # Servicios de negocio
```

### Patrones Principales

#### 1. **Repository Pattern**
```php
// Repositorio
class TenantRepository extends BaseRepository
{
    public function findByEmail(string $email): ?Tenant
    {
        return $this->model->where('email', $email)->first();
    }
}
```

#### 2. **Service Layer**
```php
// Servicio
class TenantService extends BaseModelService
{
    public function create(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            $tenant = $this->repository->create($data);
            // Lógica adicional...
            return $tenant;
        });
    }
}
```

#### 3. **Form Builder**
```php
// Formulario
public function getFormBuilder(): FormBuilder
{
    return FormBuilder::make()
        ->addField('name', 'text', ['label' => 'Nombre', 'required' => true])
        ->addField('email', 'email', ['label' => 'Email', 'required' => true]);
}
```

#### 4. **ListView Config**
```php
// Configuración de listado
public function getListViewConfig(): ListViewConfig
{
    $config = new ListViewConfig();
    
    $config->columns([
        'id' => ['label' => 'ID', 'sortable' => true],
        'name' => ['label' => 'Nombre', 'searchable' => true],
    ]);
    
    $config->addAction('Editar', 'tenant.edit', [
        'icon' => 'bi-pencil text-primary',
    ]);
    
    return $config;
}
```

## 🎯 Características Principales

### ✅ Arquitectura Modular
- Proyectos independientes en `app/Projects/`
- Código compartido en `app/Common/`
- Fácil escalabilidad

### ✅ Patrones de Diseño
- Repository Pattern para acceso a datos
- Service Layer para lógica de negocio
- Builder Pattern para formularios y listados
- Adapter Pattern para administración

### ✅ Sistema de Administración
- CRUD automático con AdminBaseAdapter
- Formularios dinámicos con FormBuilder
- Listados configurables con ListViewConfig
- StatCards para estadísticas

### ✅ UI Moderna
- Bootstrap 5
- Bootstrap Icons
- Componentes Blade reutilizables
- Diseño responsive

## 📝 Convenciones

### Nomenclatura
- **Modelos**: PascalCase (ej: `Tenant`, `User`)
- **Repositorios**: `{Model}Repository` (ej: `TenantRepository`)
- **Servicios**: `{Model}Service` (ej: `TenantService`)
- **Controladores**: `{Model}Controller` (ej: `TenantController`)
- **Adapters**: `{Model}Admin` (ej: `TenantAdmin`)

### Estructura de Archivos
```
Projects/Landlord/
├── Adapters/Admin/          # TenantAdmin, UserAdmin
├── Http/
│   ├── Controller/Admin/    # TenantAdminController
│   └── Requests/            # TenantFormRequest
├── Models/                  # Tenant, User
├── Repositories/            # TenantRepository
└── Services/
    └── Model/               # TenantService
```

## 🔧 Tecnologías

- **Backend**: Laravel 11
- **Frontend**: Blade, Bootstrap 5, Vite
- **Base de datos**: MySQL/PostgreSQL
- **Testing**: PHPUnit, Pest
- **Code Quality**: PHPStan, ECS

## 📚 Recursos Adicionales

- [Laravel Documentation](https://laravel.com/docs)
- [Bootstrap Documentation](https://getbootstrap.com/docs)
- [Bootstrap Icons](https://icons.getbootstrap.com)

## 🤝 Contribución

Para contribuir al proyecto:

1. Sigue los patrones establecidos
2. Documenta tu código
3. Escribe tests
4. Mantén la consistencia con el estilo existente

## 📄 Licencia

Este proyecto es privado y confidencial.

---

**Última actualización**: Octubre 2025
