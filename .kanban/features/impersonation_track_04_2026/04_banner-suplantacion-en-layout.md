# 04 — Banner de modo suplantación en el layout

**Track:** impersonation
**Proyecto:** core / common
**Prioridad:** medium
**Estado:** done

## Qué se hizo
- `layout_menu_sidebar.blade.php` actualizado: cuando `session('impersonator_id')` existe, muestra un banner de alerta amarillo antes del contenido principal
- El banner indica el nombre del usuario suplantado y tiene botón "Detener suplantación" (POST a `stop-impersonation`)
- La ruta se construye dinámicamente usando `ProjectManager::getCurrentProject()->getPrefix()` para ser agnóstico al proyecto
- El botón solo se renderiza si la ruta existe (`Route::has($stopRoute)`)

## Criterio de aceptación
- Al suplantar un usuario, el banner aparece en todas las páginas del admin
- Al hacer click en "Detener suplantación", vuelve al usuario original y el banner desaparece
- El banner no aparece en sesiones normales
