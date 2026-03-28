# Resumen del Proyecto — app-tenancy-projects

> Generado automáticamente por Claude Code el 2026-03-26.
> Este archivo no reemplaza la documentación oficial en `/docs/`.

---

## ¿Qué es este proyecto?

Plataforma **Laravel 11 multi-tenant modular** que permite correr múltiples proyectos independientes sobre una sola instalación. Cada cliente (tenant) opera en un esquema PostgreSQL aislado, con su propio proyecto asignado, sus propias rutas y sus propios datos.

El eje central es un **sistema de CRUD automático** que elimina código repetitivo: un solo controlador, un solo adapter declarativo y configuraciones de formulario/listado generan todas las vistas de administración.

---

## Estructura de alto nivel

```
app/
├── Common/                  ← Infraestructura compartida (Admin CRUD, Repository, Services)
│   ├── Admin/
│   │   ├── Adapter/         ← AdminBaseAdapter (base declarativa de CRUD)
│   │   ├── Controller/      ← AdminController (controlador único para LIST/CREATE/EDIT/DELETE)
│   │   ├── Config/          ← ListViewConfig, CreateViewConfig, EditViewConfig
│   │   ├── Form/            ← FormBuilder, BaseFormRequest
│   │   └── models/          ← ListColumn, ListAction, StatCard, ListFilter
│   ├── Repository/          ← BaseRepository + TransactionService
│   └── Services/            ← AlertManager
│
├── Projects/                ← Proyectos modulares independientes
│   ├── Landlord/            ← Proyecto central (gestión de tenants y usuarios)
│   ├── ActivitiesBoard/     ← Proyecto tenant (gestión de actividades)
│   └── SportCompetition/    ← Proyecto tenant (competiciones — en desarrollo mínimo)
│
├── ProjectManager.php       ← Registro y carga de proyectos activos
├── Services/
│   ├── ProjectInitService.php  ← Inicializa el proyecto según el dominio/tenant
│   └── EndpointProcessor.php   ← Extrae rutas desde atributos PHP
└── Providers/
    ├── AppServiceProvider.php
    └── TenancyServiceProvider.php
```

Cada proyecto dentro de `Projects/` sigue la misma estructura interna:

```
Projects/{NombreProyecto}/
├── Http/Controller/    ← Controladores (auth + admin)
├── Models/             ← Modelos Eloquent del proyecto
├── Services/           ← Lógica de negocio
├── Repositories/       ← Acceso a datos
├── Adapters/Admin/     ← Adapters de CRUD para cada entidad
├── FormRequests/       ← Validación de formularios
└── Providers/          ← ServiceProvider del proyecto
```

---

## Patrones de diseño implementados

| Patrón | Dónde | Para qué |
|--------|-------|---------|
| **Repository** | `Common/Repository/BaseRepository` | Abstracción de acceso a datos |
| **Service Layer** | `*/Services/` | Lógica de negocio y operaciones atómicas |
| **Adapter** | `Common/Admin/Adapter/AdminBaseAdapter` | Configuración declarativa del CRUD |
| **Builder** | `Common/Admin/Form/FormBuilder` | Construcción dinámica de formularios |
| **Attribute-based Routing** | `#[Route()]`, `#[RoutePrefix()]` | Definir rutas directamente en controllers |

---

## Tenancy (Multi-tenant)

- **Paquete**: `stancl/tenancy`
- **Aislamiento**: Esquemas PostgreSQL separados por tenant (`tenant{id}`)
- **Detección**: Middleware `InitializeTenancyByDomain` lee el subdominio
- **Dominios centrales** (sin tenancy): `localhost`, `127.0.0.1`

### Flujo de una petición

```
Request → Dominio detectado
        → Tenant buscado en BD central
        → Conexión cambiada al esquema tenant{id}
        → ProjectInitService carga el proyecto asignado al tenant
        → Middleware/routes del proyecto procesan la petición
```

### Creación de un tenant

1. Se crea registro `Tenant` en BD central
2. Se crea registro `Domain` (subdominio → tenant)
3. Evento `TenantCreated` dispara la creación del esquema PostgreSQL
4. Se ejecutan las migraciones del tenant
5. `setupDefaultSettings()` — *pendiente de implementar*

---

## Proyectos activos

### Landlord (dominio central)

Gestiona la plataforma: tenants, dominios y usuarios administradores.

**Entidades**: `Tenant`, `User`
**Prefix de rutas**: `/landlord/`

### ActivitiesBoard (proyecto tenant)

Gestión de actividades para clientes/tenants asignados.

**Entidades**: `Activity`, `User`
**Prefix de rutas**: `/activities-board/`

### SportCompetition (proyecto tenant)

Gestión de competiciones deportivas. Implementación mínima, en desarrollo.

**Entidades**: `User` (solo)
**Prefix de rutas**: `/sport-competition/`

---

## Navegación y rutas

Las rutas se generan dinámicamente mediante `EndpointProcessor` que lee los atributos `#[Route()]` y `#[RoutePrefix()]` de los controladores y las registra en `web.php` / `tenant.php`.

### Patrón general de rutas CRUD

```
GET    /{proyecto}/admin/{entidad}/list
GET    /{proyecto}/admin/{entidad}/create
POST   /{proyecto}/admin/{entidad}/create
GET    /{proyecto}/admin/{entidad}/{id}/edit
PUT    /{proyecto}/admin/{entidad}/{id}/edit
GET    /{proyecto}/admin/{entidad}/{id}/delete
DELETE /{proyecto}/admin/{entidad}/{id}/delete
GET    /{proyecto}/auth/login
POST   /{proyecto}/auth/login
```

### Rutas concretas — Landlord (dominio central)

```
GET/POST  /landlord/auth/login
GET       /landlord/admin/tenants/list
GET/POST  /landlord/admin/tenants/create
GET/PUT   /landlord/admin/tenants/{id}/edit
GET/DEL   /landlord/admin/tenants/{id}/delete
GET       /landlord/admin/users/list
GET/POST  /landlord/admin/users/create
GET/PUT   /landlord/admin/users/{id}/edit
GET/DEL   /landlord/admin/users/{id}/delete
```

### Rutas concretas — ActivitiesBoard (tenant)

```
GET/POST  /activities-board/auth/login
GET       /activities-board/admin/activities/list
GET/POST  /activities-board/admin/activities/create
GET/PUT   /activities-board/admin/activities/{id}/edit
GET/DEL   /activities-board/admin/activities/{id}/delete
GET       /activities-board/admin/users/list
GET/POST  /activities-board/admin/users/create
GET/PUT   /activities-board/admin/users/{id}/edit
GET/DEL   /activities-board/admin/users/{id}/delete
```

---

## Estado actual (rama `feature/create-module-admin`)

### Archivos modificados (sin commit)

| Archivo | Cambio |
|---------|--------|
| `app/ProjectManager.php` | Guard para evitar reinicializar el proyecto una vez cargado |
| `app/Projects/Landlord/Services/Model/TenantService.php` | Eliminada inicialización redundante del ProjectManager al crear tenant |

### Últimos commits

```
05e7b76  feat: carga del proyecto
e5f2e6f  se ignoran elementos vinculados a windsurf
6a69819  Se remueve config de proyecto de competiciones
891e901  se implementan dos proyectos de prueba
272a26e se implementa creacion de tenant
```

---

## TODOs identificados en el código

| Archivo | Línea | Descripción |
|---------|-------|-------------|
| `TenantService.php` | ~195 | `setupDefaultSettings()` — crear roles, permisos y configuración por defecto al crear tenant |
| `TenantService.php` | ~218 | `deleteSettings()` — limpieza de configuraciones al eliminar tenant |

---

## Funcionalidades pendientes / por desarrollar

- **Autenticación completa**: Los formularios de login existen pero falta gestión de sesiones/tokens y sistema de roles/permisos
- **SportCompetition**: Solo tiene `UserAdmin`, el proyecto completo está por implementar
- **Tests**: Solo existen tests de ejemplo; faltan tests unitarios y de feature para CRUD
- **Migraciones por proyecto**: Documentadas en `docs/14-migrations-por-proyecto.md` pero no completamente implementadas
- **Validación de formularios**: Los `FormRequest` existen pero tienen reglas mínimas
- **API REST**: No existe `api.php`; solo rutas web
- **Caché de proyectos/menús**: Configurada pero sin uso activo
- **Límites por tenant**: Sin sistema de cuotas (usuarios máximos, almacenamiento, etc.)

---

## Documentación disponible en `/docs/`

| Archivo | Contenido |
|---------|-----------|
| `README.md` | Guía de inicio rápido |
| `01-implementation-summary.md` | Resumen del Service Layer |
| `02-repository-pattern.md` | Guía del patrón Repository |
| `03-service-layer-pattern.md` | Guía del Service Layer |
| `04-form-builder-pattern.md` | Uso del FormBuilder |
| `05-listview-config-pattern.md` | Configuración de ListView |
| `10-tenancy-configuration.md` | Configuración multi-tenant |
| `14-migrations-por-proyecto.md` | Estrategia de migraciones por proyecto |
| `14-service-providers-by-project.md` | Registro de ServiceProviders |

---

## Archivos clave de referencia rápida

| Propósito | Archivo |
|-----------|---------|
| Registro de proyectos | `app/ProjectManager.php` |
| Inicialización por dominio | `app/Services/ProjectInitService.php` |
| Registro de rutas automático | `app/Services/EndpointProcessor.php` |
| Controlador CRUD universal | `app/Common/Admin/Controller/AdminController.php` |
| Base de adapters CRUD | `app/Common/Admin/Adapter/AdminBaseAdapter.php` |
| Constructor de formularios | `app/Common/Admin/Form/FormBuilder.php` |
| Configuración de listados | `app/Common/Admin/Config/ListViewConfig.php` |
| Acceso a datos genérico | `app/Common/Repository/BaseRepository.php` |
| Operaciones atómicas | `app/Common/Repository/Service/TransactionService.php` |
| Configuración tenancy | `config/tenancy.php` |
| Configuración proyectos | `config/projects.php` |
| Rutas centrales | `routes/web.php` |
| Rutas tenant | `routes/tenant.php` |
