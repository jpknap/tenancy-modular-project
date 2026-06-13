# 04 — Campo timezone en formulario de usuario

**Track:** timezone-config
**Proyecto:** core
**Prioridad:** medium
**Estado:** done

## Qué se hizo
- Select con `timezone_identifiers_list()` agrupado por región en `UserFormRequest` de Landlord y ActivitiesBoard
- `timezone` agregado a `$fillable` en los modelos User correspondientes
- `UserService::update()` persiste el campo
- Guardar `null` hereda timezone del tenant
