# 04 — F4: RestifyBaseRepository

**Track:** restify-api
**Proyecto:** core
**Prioridad:** high
**Estado:** backlog

Clase abstracta `RestifyBaseRepository` que extiende `Binaryk\LaravelRestify\Repositories\Repository`. Método protegido `repository()` que resuelve el repositorio vía `RepositoryManager`. Override de `newQueryWithoutScopes()` para pasar por `getQueryBuilder()` del `BaseRepository`. Métodos abstractos `fields()` y `rules()`.

**Archivos clave:**
- `app/Common/Api/Restify/RestifyBaseRepository.php` (nuevo)

**Criterio de aceptación:** Clase base instanciable. `repository()` resuelve correctamente el repositorio del proyecto activo.
