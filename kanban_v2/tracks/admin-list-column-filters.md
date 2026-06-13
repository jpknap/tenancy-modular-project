# Track — Filtros de Columnas en Listas (admin-list-column-filters)

## Estado actual
**Parcialmente implementado** — infraestructura base lista, 2 features pendientes.

## Descripción
Sistema de filtros integrado en `ListViewConfig` que permite filtrar columnas directamente desde la tabla de administración, con AJAX sin recargar la página completa.

---

## Arquitectura implementada

### Estrategia de filtros
Cada estrategia implementa `FilterStrategyInterface`:
```php
interface FilterStrategyInterface
{
    public function applyFilter(Builder $query, string $column, mixed $value): Builder;
    public function render(string $columnName, mixed $currentValue = null): string;
}
```

Ubicación: `app/Common/Admin/Services/Filters/`

### Asignar filtro a una columna (en Adapter)
```php
$config->addColumn('name', 'Nombre');
$config->getColumn('name')?->setFilter(TextFilterStrategy::class);
```

### JS — trigger y debounce
En `resources/views/landlord/list.blade.php` (línea ~173):
- Clase CSS `.column-filter-text` identifica los inputs de filtro
- Debounce de 300ms
- Trigger a **3 caracteres mínimo** (hardcodeado — pendiente hacerlo configurable)
- AJAX parcial: reemplaza solo `<tbody>` y paginación, mantiene foco del input

---

## Sub-features

| # | Feature | Estado | Notas |
|---|---------|--------|-------|
| #040 | Servicio abstracto + interface | ✅ done | `FilterStrategyInterface` + 4 strategies |
| #041 | Filtro texto (3 chars) | ✅ done | Case/accent insensitive LIKE |
| #042 | Filtro números con operadores | ⚠️ partial → pending | Solo `=` implementado |
| #043 | Filtro select (relaciones/enums) | ❌ pending | `SelectFilterStrategy` no existe aún |
| #044 | Filtro boolean (Sí/No/Todos) | ✅ done | Select con 3 opciones |

## Deuda conocida
- Mínimo de 3 chars hardcodeado en JS — Boolean y Date no lo necesitan
- Boolean/Date deberían dispararse en `change`, no en `input` con debounce
- `NumberFilterStrategy` solo soporta `=` (ver `pending/` #042)
