# 03 — Sección Configuración General en Admin (locale)

**Track:** language-config
**Proyecto:** landlord
**Prioridad:** medium
**Estado:** done

## Qué se hizo
- `SettingsController` con rutas `GET/POST /landlord/settings/general`
- `GeneralSettingsFormRequest` con campo `locale`
- Persiste `locale` del tenant en BD central
