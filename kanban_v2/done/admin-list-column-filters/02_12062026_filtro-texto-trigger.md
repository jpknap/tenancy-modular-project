# 02 — Filtro texto con trigger a 3 caracteres

**Track:** admin-list-column-filters
**Proyecto:** all
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `TextFilterStrategy::applyFilter()` genera `WHERE LOWER(REPLACE(...)) LIKE ?` normalizado sin tildes
- JS en `resources/views/landlord/list.blade.php` (línea ~173):
  - Clase CSS `.column-filter-text` para detectar inputs de filtro
  - Debounce 300ms
  - Trigger a 3 caracteres mínimo (hardcodeado — ver deuda)
  - AJAX parcial: reemplaza solo `<tbody>` y paginación, mantiene foco del input

## Deuda conocida
- Los 3 chars mínimos están hardcodeados — Boolean y Date no deberían requerir este mínimo
- Boolean/Date deberían dispararse en `change`, no en `input` con debounce
