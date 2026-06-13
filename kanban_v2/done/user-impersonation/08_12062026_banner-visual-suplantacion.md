# 08 — Banner visual de suplantación activa

**Track:** user-impersonation
**Proyecto:** core
**Prioridad:** medium
**Estado:** done

## Qué se hizo
- Banner rojo persistente en `layouts/layout_menu_sidebar.blade.php`
- Detecta `session('impersonating_user_id')` para mostrar nombre del usuario suplantado
- Botón "Salir de suplantación" con route dinámico via `ProjectManager`
- Middleware `ImpersonationHeaders` agrega `X-Impersonating: true` y `X-Impersonated-By` en cada response
