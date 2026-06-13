# 05 — Filtro Boolean (Sí/No/Todos)

**Track:** admin-list-column-filters
**Proyecto:** all
**Prioridad:** medium
**Estado:** done

## Qué se hizo
- `BooleanFilterStrategy::applyFilter()` — `WHERE column = 1/0` o sin WHERE para "Todos"
- Render: `<select>` con opciones `— Todos —`, `Sí`, `No`
- Usa `filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)` para parsear el valor

## Deuda conocida
Pasa por el mismo JS con debounce de 300ms y mínimo de 3 chars que los filtros de texto. Debería dispararse en `change` inmediatamente sin ese mínimo.
