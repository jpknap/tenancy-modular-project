# Feature: Autenticación en Landlord — Base compatible con otros proyectos

**Proyecto:** landlord / core
**Prioridad:** high
**Estado:** finalizado
**Fecha:** 2026-03-26

---

## Descripción

Actualmente `AuthController` está duplicado de forma idéntica en Landlord y ActivitiesBoard (y potencialmente SportCompetition), con paths hardcodeados y sin protección real sobre las rutas de administración. Además, el `EndpointProcessor` referencia los atributos `#[Middleware]` y `#[Where]` que aún no existen en `app/Attributes/`.

Esta feature crea una base de autenticación reutilizable en `Common/` que:
- Elimina la duplicación de `AuthController` entre proyectos
- Añade el atributo `#[Middleware]` faltante para que el sistema de rutas por atributos funcione completo
- Protege las rutas admin con un guard nombrado por proyecto
- Introduce un guard `landlord` explícito en `config/auth.php` para separar la sesión del landlord de la de proyectos tenant

## Alcance

### Incluye
- Creación del atributo `#[Middleware]` (actualmente referenciado en `EndpointProcessor` pero inexistente)
- Creación del atributo `#[Where]` (mismo caso)
- Completar la recolección de middleware a nivel de clase en `EndpointProcessor::getClassMiddleware()`
- `BaseAuthController` abstracto en `app/Common/Http/Controller/Auth/`
- Guard `landlord` en `config/auth.php` (session driver, provider central users)
- Middleware `EnsureAuthenticated` registrado como alias `auth.landlord` y `auth.tenant`
- Actualización de `AuthController` de Landlord y ActivitiesBoard para extender el base
- Protección de `AdminController` con `#[Middleware(['auth.landlord'])]`

### No incluye
- Autenticación API / JWT / Sanctum
- "Remember me" persistente
- Password reset flow (tabla ya existe pero la UI queda fuera)
- 2FA / OAuth
- Autorización por roles/policies (Gate)
- Migración nueva: los cambios son solo en código y configuración

---

## Plan de acción

1. [ ] Crear `app/Attributes/Middleware.php`
2. [ ] Crear `app/Attributes/Where.php`
3. [ ] Completar `EndpointProcessor::getClassMiddleware()` para leer `#[Middleware]` a nivel de clase
4. [ ] Crear `BaseAuthController` en `app/Common/Http/Controller/Auth/`
5. [ ] Añadir guard `landlord` en `config/auth.php`
6. [ ] Crear middleware `EnsureAuthenticated` en `app/Http/Middleware/`
7. [ ] Registrar aliases `auth.landlord` y `auth.tenant` en `bootstrap/app.php`
8. [ ] Actualizar `AuthController` de Landlord para extender `BaseAuthController`
9. [ ] Actualizar `AuthController` de ActivitiesBoard para extender `BaseAuthController`
10. [ ] Añadir `#[Middleware(['auth.landlord'])]` a `AdminController` (Common)
11. [ ] Verificar rutas con `php artisan route:list`
12. [ ] Tests manuales: login OK, login fallido, acceso a admin sin sesión redirige a login

---

## Plan de implementación técnico

### Archivos a crear

| Archivo | Descripción |
|---------|-------------|
| `app/Attributes/Middleware.php` | Atributo PHP `#[Middleware(['...'])]` para métodos y clases. Actualmente referenciado en `EndpointProcessor` pero no existe. |
| `app/Attributes/Where.php` | Atributo PHP `#[Where(['param' => 'regex'])]` para restricciones de ruta. Mismo caso. |
| `app/Common/Http/Controller/Auth/BaseAuthController.php` | Controlador abstracto con `showLogin()`, `login()`, `logout()` usando template method pattern. Métodos abstractos: `loginView()`, `defaultRedirect()`, `guard()`. |
| `app/Http/Middleware/EnsureAuthenticated.php` | Middleware que valida sesión activa para el guard indicado y redirige al login del proyecto actual si no está autenticado. |

### Archivos a modificar

| Archivo | Cambio requerido |
|---------|-----------------|
| `app/Services/EndpointProcessor.php` | Implementar el cuerpo vacío de `getClassMiddleware(ReflectionClass)` para leer `#[Middleware]` a nivel de clase e incluirlo en `processControllerMethods()`. Actualmente siempre pasa `[]` como `$classMiddleware`. |
| `config/auth.php` | Añadir guard `landlord` (session driver, provider `landlord_users`) y provider `landlord_users` (Eloquent, `App\Models\User`). El guard `web` existente queda como guard por defecto para proyectos tenant. |
| `bootstrap/app.php` | Registrar aliases de middleware: `'auth.landlord' => EnsureAuthenticated::class` con guard `landlord`, y `'auth.tenant' => EnsureAuthenticated::class` con guard `web`. |
| `app/Common/Admin/Controller/AdminController.php` | Añadir `#[Middleware(['auth.landlord'])]` a nivel de clase para que todas las rutas admin hereden la protección. (Cada proyecto podrá sobreescribir con su propio guard.) |
| `app/Projects/Landlord/Http/Controller/Auth/AuthController.php` | Extender `BaseAuthController`. Implementar `loginView()` → `'landlord.auth.login'`, `defaultRedirect()` → `'/landlord/admin/tenant/list'`, `guard()` → `'landlord'`. |
| `app/Projects/ActivitiesBoard/Http/Controller/Auth/AuthController.php` | Extender `BaseAuthController`. Implementar `loginView()` → `'activities-board.auth.login'`, `defaultRedirect()` → `'/activities-board/admin/users/list'`, `guard()` → `'web'`. |

### Migraciones necesarias
- Ninguna. La tabla `users` central ya existe. El guard `landlord` apunta al mismo modelo `App\Models\User`.

### Consideraciones técnicas

- **`EndpointProcessor` ya soporta middleware**: la infraestructura (`classMiddleware`, `methodMiddleware`, `buildEndpoint`) ya está en su lugar. Solo falta que existan los atributos y que se implemente `getClassMiddleware()`.
- **Guard `landlord` vs `web`**: el guard `landlord` usa la misma tabla `users` central pero con nombre explícito, lo que permite en el futuro separar sesiones (e.g. cookie diferente) o cambiar el model sin afectar a los tenant.
- **`EnsureAuthenticated` parametrizable**: el middleware debe recibir el nombre del guard como parámetro (`auth.landlord` pasa `landlord`, `auth.tenant` pasa `web`) para poder construir la URL de redirect al login correcta usando `ProjectManager::current()`.
- **Herencia en `EndpointProcessor::getClassPrefix()`**: ya recorre la cadena de herencia. `getClassMiddleware()` debe hacer lo mismo para que `AdminController` pueda declarar middleware base y las subclases heredarlo.
- **`AdminController` extiende para todos los proyectos**: al añadir `#[Middleware]` en `AdminController` (Common), tanto Landlord como ActivitiesBoard quedan protegidos automáticamente. Si un proyecto tenant necesita un guard distinto, su `XAdminController` puede redeclarar el middleware.

### Orden de implementación sugerido

1. Crear `app/Attributes/Middleware.php` y `app/Attributes/Where.php` — desbloquea el compilador y el `EndpointProcessor`
2. Implementar `getClassMiddleware()` en `EndpointProcessor` — activa la cadena de middleware por clase
3. Crear `BaseAuthController` — define el contrato abstracto
4. Añadir guard `landlord` en `config/auth.php` — requerido antes de crear el middleware
5. Crear `EnsureAuthenticated` y registrar aliases en `bootstrap/app.php`
6. Actualizar `AuthController` de Landlord y ActivitiesBoard
7. Añadir `#[Middleware]` en `AdminController`
8. Verificar con `php artisan route:list` y pruebas manuales

---

## Criterios de aceptación

- [ ] `php artisan route:list` no lanza errores sobre `Middleware` o `Where` inexistentes
- [ ] `GET /landlord/auth/login` responde 200 sin sesión activa
- [ ] `POST /landlord/auth/login` con credenciales válidas crea sesión y redirige a `/landlord/admin/tenant/list`
- [ ] `POST /landlord/auth/login` con credenciales inválidas vuelve al formulario con error en campo `email`
- [ ] `GET /landlord/admin/tenant/list` sin sesión redirige a `/landlord/auth/login`
- [ ] `GET /landlord/admin/tenant/list` con sesión activa responde 200
- [ ] `POST /landlord/auth/logout` destruye la sesión y redirige a `/landlord/auth/login`
- [ ] Los proyectos ActivitiesBoard y SportCompetition no se ven afectados en su flujo de login existente
- [ ] El `BaseAuthController` tiene cobertura de al menos los 3 casos (login show, login post, logout)

---

## Notas

- **`EndpointProcessor` tiene un bug latente**: el método `getClassMiddleware()` está en el bloque de comentario (líneas 106–108) y nunca fue implementado. El `$classMiddleware` siempre llega vacío. Esta feature lo implementa.
- **`AdminController` hardcodea vistas de Landlord** (`'landlord.list'`, `'landlord.new'`, etc.) — esto queda fuera del alcance de esta feature pero es deuda técnica a revisar en futura feature de separación de vistas por proyecto.
- Se descartó usar el alias `auth` de Laravel (que redirige a `/login` hardcodeado) para evitar colisión con las rutas por prefijo de proyecto.
