# 03 — Filtro Números (=, >, <, >=, <=, Entre)

**Track:** admin-list-column-filters
**Proyecto:** all
**Prioridad:** medium
**Estado:** todo

## Descripción
Filtro para columnas numéricas con operadores:

### Backend:
- `NumberFilterStrategy` con soporte operadores: `=`, `>`, `<`, `>=`, `<=`, `between`
- `applyFilter()` espera: `{operator: '>', value: 100}` o `{operator: 'between', min: 10, max: 50}`
- Genera WHERE con operador correspondiente

### Frontend:
- Dropdown select operador (default =)
- Input número validado
- Si operador = "between", muestra 2 inputs (min/max)
- Trigger inmediato al cambiar dropdown o input numérico
- Query param: `?filters[column_name][operator]=...&filters[column_name][value]=...`

### Integración:
```php
$config->getColumns()['price']->setFilter(NumberFilterStrategy::class);
```

## Criterios de Aceptación
- [ ] NumberFilterStrategy maneja todos operadores
- [ ] Dropdown y inputs renderizados
- [ ] "Between" muestra 2 inputs dinámicamente
- [ ] Trigger inmediato sin debounce
- [ ] Query params preservados
- [ ] Validación numérica en frontend
