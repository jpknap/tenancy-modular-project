# 11 — OpenAPI Documentación Automática

**Track:** restify-api
**Proyecto:** core
**Prioridad:** low
**Estado:** pending

## Qué hacer
Habilitar generación automática de OpenAPI spec vía Restify:
- `config/restify.php` → `'swagger' => true`
- Documentar cada repository con `$title` y `$description`

Endpoints disponibles:
- `GET /{project}/api/api-json` → spec JSON
- `GET /{project}/api/api-docs` → Swagger UI

## Criterio de aceptación
- `GET /activities-board/api/api-json` retorna OpenAPI spec válido con todos los endpoints del proyecto
