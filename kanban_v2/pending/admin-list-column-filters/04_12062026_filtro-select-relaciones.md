# 04 — Filtro Select para relaciones y enums

**Track:** admin-list-column-filters
**Proyecto:** all
**Prioridad:** high
**Estado:** pending

## Qué hacer

### Crear `SelectFilterStrategy`
```php
class SelectFilterStrategy implements FilterStrategyInterface
{
    // Soporta 3 modos de opciones:
    // 1. Array estático:  ['draft' => 'Borrador', 'published' => 'Publicado']
    // 2. Enum PHP:        StatusEnum::class
    // 3. Relación:        ['relation' => 'user', 'column' => 'id', 'label' => 'name']
}
```

### applyFilter según modo
- **Array/Enum**: `WHERE column = value`
- **Relación**: `whereHas('relation', fn($q) => $q->where('id', $value))`

### Frontend
- `<select>` con opciones generadas en backend (render-time)
- Opción vacía "— Todos —"
- Trigger inmediato en `change`

### Integración en Adapter
```php
// Enum
$config->getColumn('status')?->setFilter(SelectFilterStrategy::class);
// Pasar opciones via constructor o método dedicado (definir API)

// Relación
$config->getColumn('user_id')?->setFilter(SelectFilterStrategy::class);
```

## Nota de diseño
Definir cómo se pasan las opciones al `setFilter()` — actualmente `ListColumn::setFilter()` solo acepta el class string. Evaluar si se extiende la firma o se usa un DTO de configuración.

## Criterio de aceptación
- SelectFilterStrategy maneja los 3 tipos de fuente de opciones
- `whereHas()` funciona para relaciones
- Enums generan opciones desde sus cases
- Trigger inmediato al cambiar el select
