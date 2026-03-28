# Feature: Formulario de edición de perfil

**Proyecto:** core
**Prioridad:** medium
**Estado:** draft
**Fecha:** 2026-03-28

---

## Descripción

El dropdown de usuario en el topbar tiene el enlace "Mi Perfil" apuntando a `href="#"` (línea 58 de `top-bar.blade.php`). Esta feature lo conecta a una página real donde el usuario autenticado puede editar su propio nombre, email y contraseña.

Es distinto al CRUD de usuarios del admin: no usa el patrón Adapter/AdminController porque no es gestión de terceros — es el usuario editándose a sí mismo. Sigue el mismo patrón de `BaseAuthController`: un controlador abstracto en `Common/` que cada proyecto extiende con su guard y rutas propias.

## Alcance

### Incluye
- `BaseProfileController` abstracto en `Common/` con `show()` y `update()`
- `ProfileFormRequest` en `Common/` — validación de nombre, email y cambio de contraseña opcional con verificación de contraseña actual
- `ProfileController` concreto en Landlord y ActivitiesBoard extendiendo el base
- Vista `profile/edit` en cada proyecto (o una vista común reutilizable)
- `profileUrl` inyectado en `TopbarComposer` para conectar el enlace "Mi Perfil"
- Rutas `Profile` añadidas a los enums de cada proyecto
- Registro de `ProfileController` en `config/projects.php`

### No incluye
- Subida de foto de perfil / avatar
- Configuración de preferencias (idioma, zona horaria)
- Verificación de email al cambiar correo
- Historial de cambios de contraseña
- Formulario de perfil para SportCompetition (no tiene flujo de auth completo aún)

---

## Plan de acción

1. [ ] Crear `BaseProfileController` en `app/Common/Http/Controller/`
2. [ ] Crear `ProfileFormRequest` en `app/Common/Http/Requests/`
3. [ ] Crear `ProfileController` en Landlord y ActivitiesBoard
4. [ ] Añadir rutas `Profile` a los enums `Routes` de cada proyecto
5. [ ] Registrar controllers en `config/projects.php`
6. [ ] Crear vistas `profile/edit.blade.php`
7. [ ] Añadir `profileUrl` a `TopbarComposer` y conectar el enlace en `top-bar.blade.php`
8. [ ] Verificar con `php artisan route:list` y prueba manual

---

## Plan de implementación técnico

### Archivos a crear

| Archivo | Descripción |
|---------|-------------|
| `app/Common/Http/Controller/ProfileController.php` | Controlador abstracto con `show()` (GET) y `update()` (PUT). Métodos abstractos: `guard()`, `profileView()`, `profileRoute()`. |
| `app/Common/Http/Requests/ProfileFormRequest.php` | `FormRequest` de Laravel (no extiende `BaseFormRequest`). Valida `name`, `email` único excluyendo al usuario actual, `current_password` verificada contra el hash, `new_password` opcional con `confirmed`. |
| `app/Projects/Landlord/Http/Controller/ProfileController.php` | Extiende `BaseProfileController`. `guard()` → `'landlord'`, `#[Middleware(['auth.landlord'])]`. |
| `app/Projects/ActivitiesBoard/Http/Controller/ProfileController.php` | Extiende `BaseProfileController`. `guard()` → `'web'`, `#[Middleware(['auth.tenant'])]`. |
| `resources/views/landlord/profile/edit.blade.php` | Vista del formulario de perfil con secciones: datos personales y cambio de contraseña. Hereda el layout admin. |

### Archivos a modificar

| Archivo | Cambio requerido |
|---------|-----------------|
| `app/Projects/Landlord/Enums/Routes.php` | Añadir `case ProfileEdit = 'landlord.profile.edit'` |
| `app/Projects/ActivitiesBoard/Enums/Routes.php` | Añadir `case ProfileEdit = 'activities-board.profile.edit'` |
| `config/projects.php` | Registrar `ProfileController::class` en la lista de controllers de Landlord y ActivitiesBoard |
| `app/Http/View/Composers/TopbarComposer.php` | Añadir `'profileUrl'` usando `Routes::ProfileEdit->route()` según el proyecto activo |
| `resources/views/partials/top-bar.blade.php` | Reemplazar `href="#"` del ítem "Mi Perfil" por `href="{{ $topbarData['profileUrl'] }}"` |

### Migraciones necesarias
Ninguna. El modelo `User` ya tiene `name`, `email` y `password`.

### Consideraciones técnicas

- **`BaseProfileController` NO lleva `#[Middleware]`** — igual que `BaseAuthController`. El middleware va en cada subclase concreta porque el guard varía por proyecto (`auth.landlord` para Landlord, `auth.tenant` para proyectos tenant).
- **`#[RoutePrefix('profile')]`** va en el base. Genera rutas `/{prefix}/profile/edit` para cada proyecto.
- **Verificación de contraseña actual** debe hacerse en `ProfileFormRequest::withValidator()` usando `Hash::check($value, Auth::guard($guard)->user()->password)`. El guard lo pasa el controller como contexto o se resuelve desde el request.
- **Email único excluyendo al usuario actual**: `'unique:users,email,' . Auth::guard(...)->id()` — mismo patrón que `UserFormRequest`.
- **`new_password` es opcional**: solo se aplica si el campo no está vacío. La regla sería `['nullable', 'string', 'min:8', 'confirmed']` y el `update()` del base solo incluye la password en el array si está presente.
- **`UserService::update()`** ya hashea la password — se puede reutilizar directamente desde el `ProfileController`.
- **Vista reutilizable**: la vista puede vivir en `resources/views/landlord/profile/edit.blade.php` y ActivitiesBoard puede reusarla o tener la suya. Por ahora una por proyecto.
- **`TopbarComposer`**: el `$project` ya está disponible (se añadió en la feature de logout). Solo necesita añadir la entrada `profileUrl` usando el enum del proyecto activo. El cast de proyecto a enum de rutas requiere un `match` o método en el `Project`.

### Orden de implementación sugerido

1. `ProfileFormRequest` — independiente, se puede testear solo
2. `BaseProfileController` — depende de `ProfileFormRequest`
3. `ProfileController` de Landlord y ActivitiesBoard
4. Enums `Routes` — añadir `ProfileEdit`
5. `config/projects.php` — registrar controllers
6. `TopbarComposer` + `top-bar.blade.php`
7. Vistas

---

## Criterios de aceptación

- [ ] `GET /landlord/profile/edit` sin sesión redirige a `/landlord/auth/login`
- [ ] `GET /landlord/profile/edit` con sesión muestra el formulario con `name` y `email` del usuario autenticado prellenados
- [ ] `PUT /landlord/profile/edit` con datos válidos (sin cambio de contraseña) actualiza nombre y email
- [ ] `PUT /landlord/profile/edit` con `new_password` pero sin `current_password` válida devuelve error de validación
- [ ] `PUT /landlord/profile/edit` con `current_password` correcta y `new_password` válida actualiza la contraseña
- [ ] El enlace "Mi Perfil" en el topbar apunta a la URL correcta por proyecto
- [ ] La ruta aparece en `php artisan route:list` con el middleware `auth.landlord`

---

## Notas

- Se descartó extender `AdminController` porque el perfil no necesita el sistema Adapter/FormBuilder — es un formulario específico con validación propia.
- Se descartó una vista compartida entre proyectos para evitar acoplamiento; cada proyecto puede personalizar su vista de perfil independientemente.
- El `TopbarComposer` necesitará un mecanismo para resolver el `profileUrl` dinámicamente por proyecto. Una opción limpia: añadir `getRoutes()` a `ProjectInterface` que devuelva el enum de rutas del proyecto. Queda como decisión de diseño a evaluar en implementación.
