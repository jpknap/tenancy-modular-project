# 🏢 Tenancy Modular Project

Sistema multi-tenant modular construido con Laravel 11, implementando patrones de diseño robustos y arquitectura escalable.

## 📚 Documentación

**[Ver Documentación Completa →](./docs/README.md)**

### Documentos Principales

- **[00-index.md](./docs/00-index.md)** - Índice completo de documentación
- **[README.md](./docs/README.md)** - Guía de inicio rápido
- **[01-implementation-summary.md](./docs/01-implementation-summary.md)** - Arquitectura del proyecto
- **[02-repository-pattern.md](./docs/02-repository-pattern.md)** - Patrón Repository
- **[03-service-layer-pattern.md](./docs/03-service-layer-pattern.md)** - Service Layer
- **[04-form-builder-pattern.md](./docs/04-form-builder-pattern.md)** - Constructor de formularios
- **[05-listview-config-pattern.md](./docs/05-listview-config-pattern.md)** - Sistema de listados
- **[06-blade-components.md](./docs/06-blade-components.md)** - Componentes Blade
- **[07-design-guide.md](./docs/07-design-guide.md)** - Guía de diseño

## 🚀 Inicio Rápido

### Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/PostgreSQL

### Instalación

```bash
# Clonar repositorio
git clone <repository-url>
cd tenancy-modular-project

# Instalar dependencias PHP
composer install

# Instalar dependencias Node
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Migrar base de datos
php artisan migrate

# Compilar assets
npm run dev
```

### Servidor de Desarrollo

```bash
php artisan serve
```

Visita: `http://localhost:8000`

## 🏗️ Arquitectura

```
app/
├── Common/                    # Código compartido
│   ├── Admin/                # Sistema de administración
│   ├── FormBuilder/         # Constructor de formularios
│   ├── ListView/            # Sistema de listados
│   └── Repositories/        # Repositorios base
│
└── Projects/                 # Proyectos modulares
    └── Landlord/            # Proyecto principal
        ├── Adapters/        # Adaptadores
        ├── Http/            # Controllers y Requests
        ├── Models/          # Modelos Eloquent
        ├── Repositories/    # Repositorios
        └── Services/        # Servicios de negocio
```

## ✨ Características

### 🎯 Patrones Implementados

- ✅ **Repository Pattern** - Abstracción de acceso a datos
- ✅ **Service Layer** - Lógica de negocio centralizada
- ✅ **Builder Pattern** - Construcción de formularios y listados
- ✅ **Adapter Pattern** - Sistema de administración flexible

### 🎨 UI/UX

- ✅ **Bootstrap 5** - Framework CSS moderno
- ✅ **Bootstrap Icons** - Iconografía consistente
- ✅ **Blade Components** - Componentes reutilizables
- ✅ **Responsive Design** - Adaptable a todos los dispositivos

### 🔧 Sistema de Administración

- ✅ **CRUD Automático** - Con AdminBaseAdapter
- ✅ **Formularios Dinámicos** - FormBuilder configurable
- ✅ **Listados Avanzados** - ListViewConfig con columnas, acciones y estadísticas
- ✅ **StatCards** - Tarjetas de estadísticas personalizables

## 📝 Ejemplo Rápido

### Crear un CRUD Completo

```php
// 1. Repository
class TenantRepository extends BaseRepository
{
    protected function getModelClass(): string
    {
        return Tenant::class;
    }
}

// 2. Service
class TenantService extends BaseModelService
{
    public function __construct(TenantRepository $repository)
    {
        parent::__construct($repository);
    }
}

// 3. Admin Adapter
class TenantAdmin extends AdminBaseAdapter
{
    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();
        
        $config->columns([
            'id' => ['label' => 'ID', 'sortable' => true],
            'name' => ['label' => 'Nombre', 'searchable' => true],
        ]);
        
        return $config;
    }
}
```

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Con cobertura
php artisan test --coverage
```

## 📊 Code Quality

```bash
# PHPStan
./vendor/bin/phpstan analyse

# ECS (Easy Coding Standard)
./vendor/bin/ecs check
```

## 🤝 Contribución

1. Sigue los patrones establecidos en la documentación
2. Escribe tests para nuevas funcionalidades
3. Mantén la consistencia con el código existente
4. Documenta cambios significativos

## 📄 Licencia

Este proyecto es privado y confidencial.

## 🔗 Enlaces

- [Laravel Documentation](https://laravel.com/docs)
- [Bootstrap Documentation](https://getbootstrap.com/docs)
- [Documentación del Proyecto](./docs/README.md)

---

**Construido con ❤️ usando Laravel 11**
