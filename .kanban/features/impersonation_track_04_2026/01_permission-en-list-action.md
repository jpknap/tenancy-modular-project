# 01 — Soporte de `permission` y `form_method` en ListAction

**Track:** impersonation
**Proyecto:** core / common
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `ListAction` acepta nueva opción `permission` (string|null): si está definida, la acción solo se renderiza si el usuario tiene ese permiso (`auth()->user()?->can($permission)`)
- `ListAction` acepta nueva opción `form_method` (string, default `'DELETE'`): permite que acciones de tipo `form` usen métodos HTTP distintos (e.g., `POST`)
- `list.blade.php` actualizado para envolver cada acción con el check de permiso y usar `form_method` dinámicamente

## Criterio de aceptación
- Un admin sin `users:impersonate` no ve el botón de suplantar en el listado
- El delete sigue funcionando con `@method('DELETE')` sin romper nada
