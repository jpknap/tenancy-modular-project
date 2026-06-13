# 02 — F2: Middleware ApiProjectContext

**Track:** restify-api
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Nuevo middleware `ApiProjectContext` que resuelve el proyecto desde el segment `{project}` de la URL (en lugar del `current_project` del tenant). Llama `ProjectManager::getProject($project)`, instancia el proyecto e inicializa su ServiceProvider. Agrega `hasProject(string $prefix): bool` en `Tenant`.

**Archivos clave:**
- `app/Http/Middleware/ApiProjectContext.php` (nuevo)
- `app/Models/Tenant.php` → método `hasProject()`
- `bootstrap/app.php` → alias `tenant.project`

**Criterio de aceptación:** URL con proyecto inválido → 404. Proyecto correcto → RepositoryManager resuelve repositorios del proyecto.
