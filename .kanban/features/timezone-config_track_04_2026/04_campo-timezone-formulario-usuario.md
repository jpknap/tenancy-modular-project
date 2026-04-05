# 04 — Campo timezone en formulario de usuario

**Track:** timezone-config
**Proyecto:** core
**Prioridad:** medium
**Estado:** done
**Ref plan:** SF-04
**Depende de:** 01

## Qué hacer
- Agregar campo `->select('timezone', 'Zona horaria', [...])` en `UserFormRequest` de **Landlord** y **ActivitiesBoard**
- El listado de opciones usa `timezone_identifiers_list()` (PHP nativo, ~400 zonas)
- Agrupar por región para mejor UX: `America/`, `Europe/`, `UTC`, etc.
- Agregar `timezone` a `$fillable` en los modelos User correspondientes
- Actualizar `UserService::update()` para que persista el campo

## Criterio de aceptación
- El select muestra las zonas agrupadas por región
- Guardar un usuario con timezone actualiza el campo en BD
- Un usuario sin timezone seleccionada guarda `null` (hereda del tenant)
