# 03 — Filtro Números con operadores (=, >, <, >=, <=, entre)

**Track:** admin-list-column-filters
**Proyecto:** all
**Prioridad:** medium
**Estado:** pending

## Contexto
`NumberFilterStrategy` ya existe en `app/Common/Admin/Services/Filters/NumberFilterStrategy.php` pero solo implementa igualdad (`=`). Esta feature lo extiende para soportar todos los operadores.

## Qué hacer

### Backend — extender `NumberFilterStrategy`
```php
public function applyFilter(Builder $query, string $column, mixed $value): Builder
{
    // $value puede ser:
    // string simple "100" → operator =
    // array ['operator' => '>', 'value' => 100]
    // array ['operator' => 'between', 'min' => 10, 'max' => 50]
}
```

Operadores soportados: `=`, `>`, `<`, `>=`, `<=`, `between`

### Frontend
- Dropdown `<select>` con los operadores (default `=`)
- Input número validado
- Si operador = `between`: mostrar dos inputs (min/max) dinámicamente via JS
- Trigger inmediato al cambiar dropdown o salir del input
- Query params: `?filters[col][operator]=>&filters[col][value]=100`
  o para between: `?filters[col][operator]=between&filters[col][min]=10&filters[col][max]=50`

## Criterio de aceptación
- `NumberFilterStrategy` maneja los 6 operadores
- UI renderiza dropdown + inputs correctamente
- `between` muestra dinámicamente los dos inputs
- Query params se preservan en paginación
