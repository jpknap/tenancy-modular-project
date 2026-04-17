# 04 — Filtro Select para Relaciones y Enums

**Track:** admin-list-column-filters
**Proyecto:** all
**Prioridad:** high
**Estado:** todo

## Descripción
Filtro select para relaciones y enums:

### Backend:
- `SelectFilterStrategy` con opciones estáticas o dinámicas
- `applyFilter()` con:
  - Atributos directos: `WHERE column = value`
  - Relaciones: `whereHas('relation', fn => where column = value)`
  - Enums: `WHERE column = EnumClass::value`

### Construcción de opciones:
- **Enum:** `setFilter(SelectFilterStrategy::class, enum: StatusEnum::class)`
- **Relación:** `setFilter(SelectFilterStrategy::class, relation: 'user', column: 'id', label: 'name')`
- **Array estático:** `setFilter(SelectFilterStrategy::class, options: ['draft' => 'Borrador', 'published' => 'Publicado'])`

### Frontend:
- Select HTML con opciones cargadas en backend
- Trigger inmediato al cambiar
- Query param: `?filters[column_name]=value_id`

### Integración:
```php
$config->getColumns()['user_id']->setFilter(SelectFilterStrategy::class, 
    relation: 'user', 
    column: 'id', 
    label: 'name'
);

$config->getColumns()['status']->setFilter(SelectFilterStrategy::class, 
    enum: StatusEnum::class
);
```

## Criterios de Aceptación
- [ ] SelectFilterStrategy maneja 3 tipos (enum, relation, array)
- [ ] Select renderizado con opciones
- [ ] whereHas() funciona para relaciones
- [ ] Enums se convierten a select
- [ ] Trigger inmediato
- [ ] Query params preservados
