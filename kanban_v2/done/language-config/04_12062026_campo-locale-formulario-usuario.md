# 04 — Campo locale en formulario de usuario

**Track:** language-config
**Proyecto:** core
**Prioridad:** medium
**Estado:** done

## Qué se hizo
- `->select('locale', 'Idioma', ['es' => 'Español', 'en' => 'English'])` en `UserFormRequest` de Landlord y ActivitiesBoard
- `UserService::update()` persiste el campo
