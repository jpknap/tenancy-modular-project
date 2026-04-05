# 05 — Sección "Configuración General" en Admin (timezone del tenant)

**Track:** timezone-config
**Proyecto:** landlord
**Prioridad:** medium
**Estado:** done
**Ref plan:** SF-05
**Depende de:** 01
**Sinergia:** Comparte implementación con language-config SF-03. Si ese track ya creó la sección, solo agregar el campo timezone aquí.

## Qué hacer
- Si language-config ya implementó `SettingsController` y `GeneralSettingsFormRequest`: agregar campo `timezone` al formulario existente.
- Si se implementa en solitario: crear `SettingsController` con rutas `GET/POST /landlord/settings/general` y `GeneralSettingsFormRequest` con campo timezone.
- El campo es un select con `timezone_identifiers_list()` agrupado por región
- Persiste en `tenants.timezone` del tenant activo (o en la configuración global si es Landlord)

## Criterio de aceptación
- El admin puede cambiar la timezone del tenant desde la sección de configuración
- El cambio se refleja inmediatamente en el display de fechas de todos los usuarios del tenant que no tengan timezone propia
