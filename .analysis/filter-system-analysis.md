# Análisis del sistema de filtros de columnas

## Contexto

El sistema de filtros por columna está construido sobre el patrón **Strategy** con la interfaz `FilterStrategyInterface`. Cada estrategia decide cómo aplicar el filtro a una query Eloquent. El renderizado del control de UI se delega a partials Blade. Este documento refleja el estado actual tras el refactor aplicado.

---

## Mapa de archivos

### Capa de dominio / estrategias

| Archivo | Responsabilidad |
|---|---|
| `app/Common/Admin/Services/Filters/FilterStrategyInterface.php` | Contrato: `applyFilter()` + `getType()` |
| `app/Common/Admin/Services/Filters/TextFilterStrategy.php` | Filtro LIKE con normalización de acentos → tipo `'text'` |
| `app/Common/Admin/Services/Filters/NumberFilterStrategy.php` | Filtro por igualdad numérica → tipo `'number'` |
| `app/Common/Admin/Services/Filters/DateFilterStrategy.php` | Filtro por fecha exacta, parsea `dd/mm/yyyy` → tipo `'date'` |
| `app/Common/Admin/Services/Filters/BooleanFilterStrategy.php` | Filtro booleano → tipo `'boolean'` |

### Capa de configuración / modelos de vista

| Archivo | Responsabilidad |
|---|---|
| `app/Common/Admin/models/ListView/ListColumn.php` | DTO de columna; almacena estrategia y `minLength`; expone `getFilterType()` y `getFilterMinLength()` |
| `app/Common/Admin/models/ListView/ListFilter.php` | DTO de filtro de cabecera global (sistema independiente, no usa estrategias) |
| `app/Common/Admin/Config/ListViewConfig.php` | Builder de config; ejecuta `applyFilters()` iterando columnas |

### Capa de adaptadores (configuración por entidad)

| Archivo | Responsabilidad |
|---|---|
| `app/Projects/Landlord/Adapters/Admin/TenantAdmin.php` | Registra estrategias y `minLength` sobre columnas de Tenant |
| `app/Projects/Landlord/Adapters/Admin/UserAdmin.php` | Registra estrategias y `minLength` sobre columnas de User |

### Capa de vistas — partials de filtros

| Archivo | Tipo renderizado |
|---|---|
| `resources/views/components/admin/filters/text.blade.php` | `<input type="text">` con `data-event="input"` |
| `resources/views/components/admin/filters/number.blade.php` | `<input type="number">` con `data-event="input"` |
| `resources/views/components/admin/filters/date.blade.php` | `<input type="text" placeholder="dd/mm/yyyy">` con `data-event="input"` |
| `resources/views/components/admin/filters/boolean.blade.php` | `<select>` con `data-event="change"` y `@selected` procesado por Blade |

### Capa de vista principal y JS

| Archivo | Responsabilidad |
|---|---|
| `resources/views/landlord/list.blade.php` | Renderiza la tabla; carga partials con `@include`; pushea el módulo JS vía `@push('scripts')` |
| `resources/js/admin/column-filter.js` | Módulo Vite; debounce + fetch AJAX + reemplazo de DOM; se auto-inicializa al ser importado |
| `resources/js/app.js` | Entry point global de Vite; no carga `column-filter` |
| `vite.config.js` | Expone `column-filter.js` como entry point separado |

### Tests

| Archivo | Qué cubre |
|---|---|
| `tests/Unit/TextFilterStrategyTest.php` | `getType()` de las cuatro estrategias |
| `tests/Feature/TenantFilterTest.php` | Filtrado por nombre, insensible a mayúsculas y acentos |

---

## Patrones utilizados

### Strategy Pattern
`FilterStrategyInterface` define el contrato. Cada implementación encapsula la lógica de filtrado (`applyFilter`) y declara su tipo semántico (`getType`). `ListColumn` almacena el nombre de clase de la estrategia y la instancia bajo demanda.

### Builder Pattern
`ListViewConfig` actúa como builder fluido. Los adapters configuran columnas, acciones y stat cards. La asignación de estrategia y `minLength` se hace post-construcción con `setFilter(strategy, minLength)`.

### Stack de Blade (`@push` / `@stack`)
`list.blade.php` empuja `column-filter.js` al stack `'scripts'` definido en el layout. El módulo JS solo se descarga en páginas que renderizan una lista — ninguna otra vista lo carga.

---

## Estado de los problemas detectados originalmente

### ✅ HTML embebido en PHP — resuelto

`render()` fue eliminado de `FilterStrategyInterface` y de todas las estrategias. Cada estrategia ahora solo implementa `applyFilter()` y `getType()`. El HTML vive en partials Blade bajo `resources/views/components/admin/filters/`.

### ✅ Bug `@selected` en string PHP — resuelto

Al mover el HTML a un partial Blade real, `@selected` es procesado por el compilador de Blade y funciona correctamente. El `<select>` del filtro booleano preserva su valor seleccionado entre peticiones.

### ✅ `minLength` hardcodeado a 3 — resuelto

`ListColumn::setFilter(string $strategyClass, int $minLength = 1)` acepta el umbral por columna. Cada partial recibe `data-min-length` y el JS lo lee en lugar de usar el valor fijo. Valores configurados en los adapters:

| Tipo | `minLength` | Razón |
|---|---|---|
| `TextFilterStrategy` | 3 | Evita consultas con términos demasiado cortos |
| `NumberFilterStrategy` | 1 | Un dígito ya es un número válido |
| `DateFilterStrategy` | 10 | Fecha completa `dd/mm/yyyy` antes de consultar |
| `BooleanFilterStrategy` | 1 | Cualquier selección válida (`"1"` o `"0"`) tiene longitud 1 |

### ✅ Evento incorrecto en `<select>` — resuelto

El partial `boolean.blade.php` declara `data-event="change"`. El JS lee `input.dataset.event` y suscribe el evento correcto por elemento (`change` para selects, `input` para el resto). Antes el select nunca disparaba porque se escuchaba `input`, evento que los `<select>` no emiten.

### ✅ Fecha como picker nativo — resuelto

`date.blade.php` usa `type="text"` con `placeholder="dd/mm/yyyy"` y `maxlength="10"`. `DateFilterStrategy::applyFilter()` parsea el formato `d/m/Y` y convierte a `Y-m-d` para la query SQL.

### ✅ JS inline en Blade — resuelto

El bloque `<script>` fue eliminado de `list.blade.php`. El comportamiento vive en `resources/js/admin/column-filter.js`, procesado por Vite: minificado, con hash para cache-busting, y cargado únicamente en vistas de listado vía `@push('scripts')`.

### ⚠️ Instanciación de estrategia sin contenedor de Laravel — pendiente

`ListColumn::getFilter()` sigue usando `new $this->filterStrategy()` en lugar de `app($this->filterStrategy)`. Actualmente ninguna estrategia tiene dependencias inyectadas, por lo que no genera errores. Si en el futuro alguna estrategia requiriese servicios del contenedor, esta línea fallaría silenciosamente.

```php
// actual
return new $this->filterStrategy();

// correcto
return app($this->filterStrategy);
```

---

## Flujo completo actual

```
Request GET /admin/tenants?filters[name]=acme
    ↓
AdminController::list()
    ↓ lee request()->input('filters')
AdminBaseAdapter::paginateWithFilters()
    ↓
ListViewConfig::applyFilters()
    ↓ por cada filtro activo
ListColumn::getFilter()->applyFilter($query, $column, $value)
    ↓ TextFilterStrategy aplica LIKE normalizado
$query->paginate()
    ↓
list.blade.php renderiza tabla
    ↓ por cada columna con filtro
@include('components.admin.filters.text', [columnName, currentValue, minLength])
    ↓
@push('scripts') → @vite('column-filter.js') → inyectado en @stack('scripts') del layout
    ↓
Browser descarga column-filter.js (minificado, cacheado con hash)
    ↓ usuario escribe en input
debounce 300ms → fetch con filtro en URL → reemplaza tbody + paginación
```
