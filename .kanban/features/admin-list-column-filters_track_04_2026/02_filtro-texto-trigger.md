# 02 — Filtro Texto con Trigger a 3 Caracteres

**Track:** admin-list-column-filters
**Proyecto:** all
**Prioridad:** high
**Estado:** todo

## Descripción
Implementar filtro de texto en columnas:

### Backend:
- `TextFilterStrategy` implementa FilterStrategyInterface
- `applyFilter(Builder $query, string $column, mixed $value)` genera `WHERE column LIKE %value%` (case-insensitive)

### Frontend:
- Input text bajo cada columna con clase `column-filter-text`
- JavaScript: debounce 300ms + trigger mínimo 3 caracteres
- Envía GET a misma ruta con query param: `?filters[column_name]=valor`
- Persiste en paginación

### Integración en Adapter:
```php
$config->getColumns()['name']->setFilter(TextFilterStrategy::class);
```

## Criterios de Aceptación
- [ ] TextFilterStrategy crea LIKE query correcto
- [ ] Input text renderizado bajo columna
- [ ] Debounce 300ms funciona
- [ ] Trigger a 3 caracteres
- [ ] Query params se preservan en paginate
- [ ] Limpia filtro si input vacío
