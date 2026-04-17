# 01 — Servicio Abstracto de Filtros para Modelos

**Track:** admin-list-column-filters
**Proyecto:** core
**Prioridad:** high
**Estado:** todo

## Descripción
Crear infraestructura base para filtros integrados en ListViewConfig:

### Clases a crear:
- `app/Common/Admin/Models/ListView/ListColumnFilter.php` — DTO con config del filtro
- `app/Common/Admin/Services/Filters/FilterStrategyInterface.php` — Interfaz que define contrato
- `app/Common/Admin/Services/Filters/TextFilterStrategy.php`
- `app/Common/Admin/Services/Filters/NumberFilterStrategy.php`
- `app/Common/Admin/Services/Filters/SelectFilterStrategy.php`
- `app/Common/Admin/Services/Filters/BooleanFilterStrategy.php`

### Integración:
- `ListColumn` agrega método `setFilter(FilterStrategy, config)` fluent
- `ListViewConfig` agrega método `applyFilters(Builder $query, array $filters)` que delega a estrategias
- Cada estrategia implementa `applyFilter(Builder $query, string $column, mixed $value): Builder`

## Criterios de Aceptación
- [ ] Interface define contrato claro
- [ ] Todas las 4 estrategias implementadas
- [ ] ListColumn integrado con setter fluent
- [ ] Estrategias manejan atributos propios
- [ ] Permite override en subclases para relaciones
