# 05 — Filtro Boolean (Sí/No/Todos)

**Track:** admin-list-column-filters
**Proyecto:** all
**Prioridad:** medium
**Estado:** todo

## Descripción
Filtro para columnas booleanas:

### Backend:
- `BooleanFilterStrategy` maneja 3 estados: true, false, null (todos)
- `applyFilter()` genera:
  - `WHERE column = true` (1)
  - `WHERE column = false` (0)
  - Sin WHERE (sin filtro)

### Frontend:
- Radio buttons o select con 3 opciones: Sí, No, Todos (default)
- Trigger inmediato sin debounce
- Query param: `?filters[column_name]=true|false|all` (o vacío = todos)

### Integración:
```php
$config->getColumns()['is_active']->setFilter(BooleanFilterStrategy::class);
```

## Criterios de Aceptación
- [ ] BooleanFilterStrategy crea WHERE correcto
- [ ] Radio buttons (o select) renderizados
- [ ] 3 opciones: Sí, No, Todos
- [ ] Trigger inmediato
- [ ] "Todos" limpia filtro
- [ ] Query params preservados
- [ ] CSS compacto, no ocupa espacio
