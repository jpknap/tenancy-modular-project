# Feature: Cierre de sesión — Base compatible con otros proyectos

**Proyecto:** core
**Prioridad:** high
**Estado:** finalizado
**Fecha:** 2026-03-26

---

## Descripción

La ruta `POST /{prefix}/auth/logout` ya existe en `BaseAuthController` y funciona correctamente. El problema está en la UI: el botón "Cerrar sesión" en `partials/top-bar.blade.php` (línea 62) apunta a `href="#"` — es un enlace muerto que no hace nada.

El logout en Laravel **exige** un `POST` con token CSRF para protección contra CSRF. Un `<a href>` nunca puede cumplir ese requisito.

Esta feature conecta la UI con la ruta real inyectando la URL de logout dinámica desde `TopbarComposer` vía `ProjectManager`, y reemplaza el enlace por un `<form method="POST">` con `@csrf`.

## Alcance

### Incluye
- Añadir `logoutUrl` a `$topbarData` en `TopbarComposer` usando `ProjectManager`
- Reemplazar el `<a href="#">Cerrar sesión</a>` en `top-bar.blade.php` por un `<form POST>` con CSRF
- El guard correcto lo maneja `BaseAuthController::logout()` — no requiere cambios

### No incluye
- Confirmación modal antes de cerrar sesión
- "Cerrar todas las sesiones" / logout de otros dispositivos
- Cambios en la lógica de `BaseAuthController` (ya está implementada)
- Rutas de logout para SportCompetition (no tiene AuthController todavía)

---

## Plan de acción

1. [ ] Actualizar `TopbarComposer` para inyectar `logoutUrl` en `$topbarData`
2. [ ] Reemplazar el enlace muerto en `top-bar.blade.php` con un `<form>` POST+CSRF
3. [ ] Verificar manualmente en Landlord y ActivitiesBoard

---

## Plan de implementación técnico

### Archivos a crear
Ninguno.

### Archivos a modificar

| Archivo | Cambio requerido |
|---------|-----------------|
| `app/Http/View/Composers/TopbarComposer.php` | Inyectar `'logoutUrl'` en `$topbarData` derivándola del prefix del proyecto actual (`ProjectManager::getCurrentProject()->getPrefix()`) |
| `resources/views/partials/top-bar.blade.php` | Reemplazar `<a href="#">Cerrar sesión</a>` (línea 62) por `<form method="POST" action="{{ $topbarData['logoutUrl'] }}">@csrf<button ...>Cerrar sesión</button></form>` |

### Migraciones necesarias
Ninguna.

### Consideraciones técnicas

- **`ProjectManager::getCurrentProject()`** puede devolver `null` si el composer se ejecuta fuera del contexto de un proyecto inicializado (e.g., en rutas sin `ProjectInitialized` middleware). `TopbarComposer` debe manejar este caso con un fallback vacío o `null`.
- **Patrón de la URL**: `/{prefix}/auth/logout` — el prefix es `landlord`, `activities-board`, etc. Se construye como `'/' . $project->getPrefix() . '/auth/logout'`.
- **El `<form>` debe tener `@csrf`** — sin él Laravel rechaza el POST con `419 Page Expired`.
- **Estilo del botón**: para mantener la apariencia del dropdown de Bootstrap, el `<button>` debe tener las clases `dropdown-item py-2 text-danger border-0 bg-transparent w-100 text-start` o equivalentes, replicando el estilo actual del `<a>`.

### Orden de implementación sugerido

1. `TopbarComposer` — primero para tener la variable disponible en la vista
2. `top-bar.blade.php` — sustituir el enlace usando `$topbarData['logoutUrl']`

---

## Criterios de aceptación

- [ ] Hacer clic en "Cerrar sesión" en el topbar de Landlord ejecuta el POST y destruye la sesión
- [ ] Tras el logout, el navegador redirige a `/landlord/auth/login`
- [ ] Acceder a `/landlord/admin/tenant/list` después del logout redirige a login (middleware `auth.landlord` activo)
- [ ] El token CSRF está presente en el form (no hay error 419)
- [ ] El botón mantiene la apariencia visual del dropdown (mismo estilo que el resto de items)

---

## Notas

- El `TopbarComposer` actualmente retorna un usuario demo hardcodeado cuando `Auth::user()` es `null` (líneas 14-19). Eso es deuda técnica separada — esta feature no lo toca.
- Se descartó un Blade component `<x-logout-form>` por ser innecesario: el form solo aparece en un lugar (`top-bar.blade.php`). Una abstracción prematura.
