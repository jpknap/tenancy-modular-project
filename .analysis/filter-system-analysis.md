# Análisis del sistema de filtros de columnas

## Contexto

El sistema de filtros por columna está construido sobre el patrón **Strategy** con la interfaz `FilterStrategyInterface`. Cada estrategia decide cómo aplicar el filtro a una query Eloquent y cómo renderizar el control de UI. Este análisis detalla los archivos involucrados, los patrones utilizados, los problemas detectados y la propuesta de refactor.

---

## Mapa de archivos

### Capa de dominio / estrategias

| Archivo | Responsabilidad |
|---|---|
| `app/Common/Admin/Services/Filters/FilterStrategyInterface.php` | Contrato: `applyFilter()` + `render()` |
| `app/Common/Admin/Services/Filters/TextFilterStrategy.php` | Filtro LIKE con normalización de acentos |
| `app/Common/Admin/Services/Filters/NumberFilterStrategy.php` | Filtro por igualdad numérica (`=`) |
| `app/Common/Admin/Services/Filters/DateFilterStrategy.php` | Filtro por fecha exacta (`whereDate`) |
| `app/Common/Admin/Services/Filters/BooleanFilterStrategy.php` | Filtro booleano con `<select>` |

### Capa de configuración / modelos de vista

| Archivo | Responsabilidad |
|---|---|
| `app/Common/Admin/models/ListView/ListColumn.php` | DTO de columna; guarda el `$filterStrategy` y lo instancia con `new` |
| `app/Common/Admin/models/ListView/ListFilter.php` | DTO de filtro de cabecera global (no usa estrategias, distinto sistema) |
| `app/Common/Admin/Config/ListViewConfig.php` | Builder de config; ejecuta `applyFilters()` iterando columnas |

### Capa de adaptadores (configuración por entidad)

| Archivo | Responsabilidad |
|---|---|
| `app/Projects/Landlord/Adapters/Admin/TenantAdmin.php` | Registra estrategias sobre columnas de Tenant |
| `app/Projects/Landlord/Adapters/Admin/UserAdmin.php` | Registra estrategias sobre columnas de User |

### Capa de orquestación

| Archivo | Responsabilidad |
|---|---|
| `app/Common/Admin/Adapter/AdminBaseAdapter.php` | `paginateWithFilters()` delega en `ListViewConfig::applyFilters()` |
| `app/Common/Admin/Controller/AdminController.php` | Lee `request()->input('filters')` y lo pasa al adapter |

### Capa de vista + JS

| Archivo | Responsabilidad |
|---|---|
| `resources/views/landlord/list.blade.php` | Renderiza la tabla; llama a `$column->getFilter()->render()`; contiene el `<script>` de filtrado |
| `resources/js/app.js` | Entry point de Vite; importa Bootstrap; **no importa ningún módulo de filtros** |

### Tests

| Archivo | Qué cubre |
|---|---|
| `tests/Unit/TextFilterStrategyTest.php` | Atributos HTML del input y escape XSS |
| `tests/Feature/TenantFilterTest.php` | Filtrado por nombre, insensible a mayúsculas y acentos |

---

## Patrones utilizados

### Strategy Pattern
`FilterStrategyInterface` define el contrato. Cada implementación encapsula la lógica de filtrado (`applyFilter`) y el renderizado (`render`). `ListColumn` almacena el nombre de clase de la estrategia y la instancia bajo demanda.

### Builder Pattern
`ListViewConfig` actúa como builder fluido. Los adapters (`TenantAdmin`, `UserAdmin`) lo configuran con columnas, acciones y stat cards. La asignación de estrategias se hace post-construcción con `setFilter()`.

### Template Method (parcial)
`AdminBaseAdapter::getListViewConfig()` provee una implementación base con columnas `id` y `created_at`. Los adapters concretos la sobreescriben completamente (no llaman a `parent`), lo que implica que la base no aporta reutilización real en la práctica actual.

---

## Problemas detectados

### 1. HTML embebido en clases PHP (violación de SoC)

**Archivos afectados:** las cuatro estrategias (`TextFilterStrategy`, `NumberFilterStrategy`, `DateFilterStrategy`, `BooleanFilterStrategy`).

El método `render()` devuelve HTML crudo usando PHP heredoc:

```php
// TextFilterStrategy.php:28-39
return <<<HTML
<input
    type="text"
    class="form-control form-control-sm column-filter-text"
    ...
>
HTML;
```

Las estrategias son servicios de dominio (aplican filtros a queries Eloquent). El hecho de que también generen HTML acopla la capa de servicio a la capa de presentación. Esto dificulta cambiar el markup sin tocar la lógica de negocio y viceversa.

---

### 2. Bug: directiva Blade `@selected` en string PHP

**Archivo:** `BooleanFilterStrategy.php:37-38`

```php
// Este código NO funciona como se espera
return <<<HTML
    <option value="1" @selected($value === '1')>Sí</option>
    <option value="0" @selected($value === '0')>No</option>
HTML;
```

La directiva `@selected(...)` es compilada por el motor de plantillas Blade. Sin embargo, este string es generado directamente por PHP y **nunca pasa por el compilador de Blade**. El resultado en el browser es que se renderiza literalmente el texto `@selected(false)` como atributo, en lugar de `selected` o nada. El `<select>` nunca mantiene su valor seleccionado entre peticiones.

La corrección en PHP puro sería:
```php
($value === '1' ? 'selected' : '')
```

---

### 3. Longitud mínima de activación hardcodeada a 3

**Archivo:** `resources/views/landlord/list.blade.php:189`

```javascript
if (filterValue.length >= 3) {
    searchParams.set(`filters[${columnName}]`, filterValue);
} else {
    searchParams.delete(`filters[${columnName}]`);
}
```

El umbral de 3 caracteres está fijo en el JS. Esto es incorrecto para:
- **Filtros numéricos:** buscar `1` o `42` nunca dispara la petición.
- **Filtros de fecha:** el valor `type="date"` se envía como `YYYY-MM-DD` (siempre 10 chars), pero el comportamiento correcto es disparar al seleccionar cualquier fecha.
- **Filtros booleanos (`<select>`):** el `<select>` cambia a un valor inmediatamente, nunca espera 3 caracteres — pero el listener es el mismo `input`, no `change`.

No hay forma de configurar este umbral por columna o por tipo de estrategia.

---

### 4. JavaScript inline en la vista Blade

**Archivo:** `resources/views/landlord/list.blade.php:172-227`

Todo el comportamiento del filtrado (debounce de 300ms, fetch AJAX, parseo del HTML de respuesta, reemplazo de DOM, preservación del foco) está en un bloque `<script>` al final de la vista. Esto:

- Impide reutilizar la lógica en otras vistas de listado.
- Mezcla preocupaciones de vista con JS.
- No puede beneficiarse del árbol de dependencias de Vite (tree-shaking, bundling).
- No puede ser testeado con herramientas JS.

El entry point `resources/js/app.js` importa Bootstrap pero no hace referencia alguna al sistema de filtros.

---

### 5. Instanciación de estrategia sin contenedor de Laravel

**Archivo:** `app/Common/Admin/models/ListView/ListColumn.php:121`

```php
public function getFilter(): ?object
{
    return new $this->filterStrategy();
}
```

La estrategia se instancia directamente con `new`. Si alguna estrategia requiriese dependencias inyectadas (repositorios, config, etc.), esto fallaría. El patrón correcto es `app($this->filterStrategy)`.

---

## Propuesta de refactor (sin romper la arquitectura)

El refactor debe respetar: el sistema de atributos para rutas, el patrón de adapters, los blade views en `resources/views/landlord/`, y la organización de `app/Common/`.

### A. Separar `render()` de la interfaz — Blade partials por tipo

**Eliminar** `render()` de `FilterStrategyInterface`. Las estrategias solo implementan `applyFilter()`.

Crear partials Blade en:
```
resources/views/components/admin/filters/
├── text.blade.php
├── number.blade.php
├── date.blade.php
└── boolean.blade.php
```

En `list.blade.php`, reemplazar `{!! $column->getFilter()->render(...) !!}` por:
```blade
@include('components.admin.filters.' . $column->getFilterType(), [
    'columnName' => $column->getKey(),
    'currentValue' => request()->input("filters.{$column->getKey()}"),
    'minLength' => $column->getFilterMinLength(),
])
```

Esto mueve el HTML a Blade (donde pertenece) y elimina el bug de `@selected`.

### B. Añadir `minLength` configurable por columna

En `ListColumn`, añadir:
```php
private int $filterMinLength = 1;

public function setFilter(string $strategyClass, int $minLength = 1): self
{
    $this->filterStrategy = $strategyClass;
    $this->filterMinLength = $minLength;
    return $this;
}

public function getFilterMinLength(): int
{
    return $this->filterMinLength;
}
```

En los adapters, el valor por defecto sería `1` para `NumberFilterStrategy`, `DateFilterStrategy`, y `BooleanFilterStrategy`, y `3` para `TextFilterStrategy`.

### C. Extraer el JS a un módulo en `resources/js/`

Crear `resources/js/admin/column-filter.js` con la lógica de debounce, fetch y reemplazo de DOM. El JS leería el `minLength` desde un `data-min-length` en cada input/select (inyectado por el partial Blade):

```javascript
// resources/js/admin/column-filter.js
export function initColumnFilters() {
    const filterInputs = document.querySelectorAll('.column-filter');
    let debounceTimer;

    filterInputs.forEach(input => {
        const eventName = input.tagName === 'SELECT' ? 'change' : 'input';
        const minLength = parseInt(input.dataset.minLength ?? '1', 10);

        input.addEventListener(eventName, function () {
            clearTimeout(debounceTimer);
            const focusedInput = this;

            debounceTimer = setTimeout(() => {
                const filterValue = this.value.trim();
                const columnName = this.dataset.column;
                const url = new URL(window.location.href);
                const params = new URLSearchParams(url.search);

                if (filterValue.length >= minLength) {
                    params.set(`filters[${columnName}]`, filterValue);
                } else {
                    params.delete(`filters[${columnName}]`);
                }

                url.search = params.toString();
                fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => {
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const newTbody = doc.querySelector('tbody');
                        const newPagination = doc.querySelector('.pagination-container');
                        if (newTbody) document.querySelector('tbody').innerHTML = newTbody.innerHTML;
                        if (newPagination) document.querySelector('.pagination-container').innerHTML = newPagination.innerHTML;
                        window.history.replaceState({}, '', url.toString());
                        focusedInput.focus();
                    });
            }, 300);
        });
    });
}
```

Importar en `app.js`:
```javascript
import { initColumnFilters } from './admin/column-filter';
document.addEventListener('DOMContentLoaded', initColumnFilters);
```

### D. Usar el contenedor de Laravel para instanciar estrategias

En `ListColumn::getFilter()`:
```php
public function getFilter(): ?object
{
    if ($this->filterStrategy === null) {
        return null;
    }
    return app($this->filterStrategy);
}
```

---

## Resumen de cambios por archivo

| Archivo | Cambio |
|---|---|
| `FilterStrategyInterface.php` | Eliminar `render()` del contrato |
| `TextFilterStrategy.php` | Eliminar `render()`; añadir `getType(): string` → `'text'` |
| `NumberFilterStrategy.php` | Eliminar `render()`; añadir `getType()` → `'number'` |
| `DateFilterStrategy.php` | Eliminar `render()`; añadir `getType()` → `'date'` |
| `BooleanFilterStrategy.php` | Eliminar `render()` (con su bug de `@selected`); añadir `getType()` → `'boolean'` |
| `ListColumn.php` | Añadir `$filterMinLength`; `setFilter()` recibe `minLength`; `getFilter()` usa `app()`; añadir `getFilterType()` |
| `resources/views/components/admin/filters/*.blade.php` | Nuevos partials con HTML limpio y `data-min-length` |
| `resources/views/landlord/list.blade.php` | Reemplazar `{!! render() !!}` por `@include`; eliminar `<script>` inline |
| `resources/js/admin/column-filter.js` | Nuevo módulo JS con lógica de filtros |
| `resources/js/app.js` | Importar e inicializar `column-filter.js` |
| `TenantAdmin.php` / `UserAdmin.php` | Ajustar `setFilter()` para pasar `minLength` adecuado por tipo |
| `tests/Unit/TextFilterStrategyTest.php` | Actualizar: `render()` ya no existe en la estrategia; testear `getType()` |

---

## Lo que NO debe cambiar

- El patrón Strategy: `FilterStrategyInterface` permanece, solo pierde `render()`.
- El flujo completo `Adapter → ListViewConfig::applyFilters() → Strategy::applyFilter()`.
- La forma en que los adapters registran estrategias con `setFilter()` (solo se añade el parámetro `minLength`).
- El routing por atributos PHP 8: `AdminController` no cambia.
- La convención de vistas en `resources/views/landlord/`.
