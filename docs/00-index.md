# 📑 Índice de Documentación - Tenancy Modular

## 🗂️ Estructura de Documentación

```
docs/
├── 00-index.md                          # Este archivo - Índice general
├── README.md                            # Introducción y guía rápida
│
├── 📐 Arquitectura y Patrones
│   ├── 01-implementation-summary.md     # Resumen de implementación
│   ├── 02-repository-pattern.md         # Patrón Repository
│   └── 03-service-layer-pattern.md      # Patrón Service Layer
│
├── 🎨 UI y Componentes
│   ├── 04-form-builder-pattern.md       # Constructor de formularios
│   ├── 05-listview-config-pattern.md    # Sistema de listados
│   ├── 06-blade-components.md           # Componentes Blade
│   ├── 07-design-guide.md               # Guía de diseño
│   └── 08-admin-view-config-pattern.md  # Sistema de configuración de vistas admin
│
└── 📚 Ejemplos
    └── blade-components-examples.md     # Ejemplos de componentes
```

## 📖 Guía de Lectura Recomendada

### Para Nuevos Desarrolladores

1. **Empieza aquí**: [README.md](./README.md)
   - Visión general del proyecto
   - Tecnologías utilizadas
   - Convenciones básicas

2. **Arquitectura**: [01-implementation-summary.md](./01-implementation-summary.md)
   - Estructura del proyecto
   - Módulos y organización
   - Flujo de trabajo

3. **Patrones Backend**:
   - [02-repository-pattern.md](./02-repository-pattern.md) - Acceso a datos
   - [03-service-layer-pattern.md](./03-service-layer-pattern.md) - Lógica de negocio

4. **Patrones Frontend**:
   - [04-form-builder-pattern.md](./04-form-builder-pattern.md) - Formularios
   - [05-listview-config-pattern.md](./05-listview-config-pattern.md) - Listados
   - [06-blade-components.md](./06-blade-components.md) - Componentes UI

5. **Diseño**: [07-design-guide.md](./07-design-guide.md)
   - Colores y tipografía
   - Componentes visuales
   - Guía de estilo

### Para Desarrolladores Experimentados

Si ya conoces Laravel y patrones de diseño:

1. [01-implementation-summary.md](./01-implementation-summary.md) - Arquitectura específica del proyecto
2. [05-listview-config-pattern.md](./05-listview-config-pattern.md) - Sistema de listados personalizado
3. [04-form-builder-pattern.md](./04-form-builder-pattern.md) - Constructor de formularios

## 🎯 Documentos por Tema

### 🏗️ Arquitectura

| Documento | Descripción | Nivel |
|-----------|-------------|-------|
| [01-implementation-summary.md](./01-implementation-summary.md) | Arquitectura modular, estructura de carpetas | Básico |
| [02-repository-pattern.md](./02-repository-pattern.md) | Patrón Repository para acceso a datos | Intermedio |
| [03-service-layer-pattern.md](./03-service-layer-pattern.md) | Service Layer para lógica de negocio | Intermedio |

### 🎨 Frontend

| Documento | Descripción | Nivel |
|-----------|-------------|-------|
| [04-form-builder-pattern.md](./04-form-builder-pattern.md) | Constructor dinámico de formularios | Intermedio |
| [05-listview-config-pattern.md](./05-listview-config-pattern.md) | Sistema de listados con columnas, acciones y stats | Avanzado |
| [06-blade-components.md](./06-blade-components.md) | Componentes Blade reutilizables | Básico |
| [07-design-guide.md](./07-design-guide.md) | Guía de diseño visual | Básico |
| [08-admin-view-config-pattern.md](./08-admin-view-config-pattern.md) | Sistema de configuración de vistas admin (List + Create) | Avanzado |

### 📚 Ejemplos

| Documento | Descripción |
|-----------|-------------|
| [blade-components-examples.md](./blade-components-examples.md) | Ejemplos prácticos de componentes |

## 🔍 Búsqueda Rápida

### Quiero crear un nuevo módulo
→ [01-implementation-summary.md](./01-implementation-summary.md) - Sección "Estructura de Proyectos"

### Quiero crear un CRUD
→ [02-repository-pattern.md](./02-repository-pattern.md) + [03-service-layer-pattern.md](./03-service-layer-pattern.md)

### Quiero crear un formulario
→ [04-form-builder-pattern.md](./04-form-builder-pattern.md)

### Quiero crear un listado
→ [05-listview-config-pattern.md](./05-listview-config-pattern.md)

### Quiero configurar vistas de admin (List + Create)
→ [08-admin-view-config-pattern.md](./08-admin-view-config-pattern.md)

### Quiero agregar tarjetas de estadísticas
→ [05-listview-config-pattern.md](./05-listview-config-pattern.md) - Sección "StatCard"
→ [08-admin-view-config-pattern.md](./08-admin-view-config-pattern.md) - ListViewConfig

### Quiero crear un componente UI
→ [06-blade-components.md](./06-blade-components.md)

### Quiero saber los colores del proyecto
→ [07-design-guide.md](./07-design-guide.md)

## 📊 Mapa de Patrones

```
┌─────────────────────────────────────────────────────────────┐
│                     ARQUITECTURA MODULAR                     │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐      ┌──────────────┐      ┌──────────┐  │
│  │  Controller  │─────▶│   Service    │─────▶│Repository│  │
│  │   (HTTP)     │      │  (Business)  │      │  (Data)  │  │
│  └──────────────┘      └──────────────┘      └──────────┘  │
│         │                      │                     │       │
│         │                      │                     │       │
│         ▼                      ▼                     ▼       │
│  ┌──────────────┐      ┌──────────────┐      ┌──────────┐  │
│  │ FormBuilder  │      │  Validation  │      │  Model   │  │
│  │ ListViewCfg  │      │ Transactions │      │Eloquent  │  │
│  └──────────────┘      └──────────────┘      └──────────┘  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

## 🚀 Flujo de Trabajo Típico

### Crear un nuevo módulo CRUD

1. **Modelo**: Crear en `app/Projects/{Project}/Models/`
2. **Repository**: Crear en `app/Projects/{Project}/Repositories/`
   - Extender `BaseRepository`
   - Ver: [02-repository-pattern.md](./02-repository-pattern.md)

3. **Service**: Crear en `app/Projects/{Project}/Services/Model/`
   - Extender `BaseModelService`
   - Ver: [03-service-layer-pattern.md](./03-service-layer-pattern.md)

4. **FormRequest**: Crear en `app/Projects/{Project}/Http/Requests/`
   - Implementar `getFormBuilder()`
   - Ver: [04-form-builder-pattern.md](./04-form-builder-pattern.md)

5. **Admin Adapter**: Crear en `app/Projects/{Project}/Adapters/Admin/`
   - Extender `AdminBaseAdapter`
   - Implementar `getListViewConfig()`
   - Ver: [05-listview-config-pattern.md](./05-listview-config-pattern.md)

6. **Controller**: Crear en `app/Projects/{Project}/Http/Controller/Admin/`
   - Extender `AdminController`

## 📝 Convenciones del Proyecto

### Nomenclatura de Archivos

```
TenantRepository.php          # Repository
TenantService.php            # Service
TenantFormRequest.php        # Form Request
TenantAdmin.php              # Admin Adapter
TenantAdminController.php    # Controller
```

### Namespace

```php
// Repository
namespace App\Projects\Landlord\Repositories;

// Service
namespace App\Projects\Landlord\Services\Model;

// Controller
namespace App\Projects\Landlord\Http\Controller\Admin;

// Adapter
namespace App\Projects\Landlord\Adapters\Admin;
```

## 🎓 Glosario

| Término | Descripción |
|---------|-------------|
| **Tenant** | Cliente/inquilino en arquitectura multi-tenant |
| **Landlord** | Proyecto principal que gestiona tenants |
| **Repository** | Capa de abstracción para acceso a datos |
| **Service** | Capa de lógica de negocio |
| **Adapter** | Adaptador para sistema de administración |
| **FormBuilder** | Constructor dinámico de formularios |
| **ListViewConfig** | Configuración de listados/tablas |
| **StatCard** | Tarjeta de estadística en listados |

## 🔗 Enlaces Útiles

- [Laravel Documentation](https://laravel.com/docs)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3)
- [Bootstrap Icons](https://icons.getbootstrap.com)
- [Blade Templates](https://laravel.com/docs/blade)

## 📞 Soporte

Para preguntas o dudas sobre la documentación:
1. Revisa primero el documento correspondiente
2. Busca en los ejemplos prácticos
3. Consulta el código fuente como referencia

---

**Última actualización**: Octubre 2025
**Versión**: 1.0
