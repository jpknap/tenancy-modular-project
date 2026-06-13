# 11 — F11: OpenAPI Documentación Automática

**Track:** restify-api
**Proyecto:** core
**Prioridad:** low
**Estado:** backlog

Habilitar generación automática de OpenAPI spec vía Restify. Documentar cada repository con `$title` y `$description`. Endpoint `GET /{project}/api/api-json` expone el spec JSON. Endpoint UI `GET /{project}/api/api-docs` expone Swagger UI.

**Archivos clave:**
- `config/restify.php` → `'swagger' => true`
- Cada `*RestifyRepository.php` → `public static string $title` y `$description`

**Criterio de aceptación:** `GET /activities-board/api/api-json` retorna OpenAPI spec válido con todos los endpoints del proyecto.
