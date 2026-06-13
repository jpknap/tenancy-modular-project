# 01 — Servicio abstracto de filtros (Interface + estrategias)

**Track:** admin-list-column-filters
**Proyecto:** core
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `FilterStrategyInterface` con `applyFilter()` y `render()`
- `TextFilterStrategy` — LIKE case/accent insensitive
- `NumberFilterStrategy` — igualdad `=` (operadores > < entre pendientes en #042)
- `BooleanFilterStrategy` — select Sí/No/Todos
- `DateFilterStrategy` — `whereDate()` exacto
- `ListColumn::setFilter(string $strategyClass)` y `getFilter(): object`
- `ListViewConfig::applyFilters(Builder $query, array $filters)` delega a cada estrategia

## Archivos clave
- `app/Common/Admin/Services/Filters/` (directorio con 5 archivos)
- `app/Common/Admin/models/ListView/ListColumn.php`
- `app/Common/Admin/Config/ListViewConfig.php`
