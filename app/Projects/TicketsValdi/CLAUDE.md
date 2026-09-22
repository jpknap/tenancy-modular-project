# TicketsValdi

Contexto para agentes al trabajar dentro de `app/Projects/TicketsValdi/`.
Complementa el `CLAUDE.md` de la raíz (comandos, arquitectura general del monorepo); no lo reemplaza.

## Identity

| | |
|---|---|
| Dominio | Venta y validación de entradas para eventos (no es un helpdesk) |
| Prefijo | `tickets-valdi` |
| Clase | `App\Projects\TicketsValdi\TicketsValdiProject` |
| Guard | `web` (usuarios del schema del tenant, **no** `landlord`) |
| Dónde corre | Subdominios de tenant + dominio central |
| Estado | Base operativa; dominio especificado en `docs/DOMAIN.md`, sin migraciones |

## Before you edit

1. **Las rutas no se declaran, se derivan.** Salen de atributos PHP (`#[RoutePrefix]`,
   `#[Route]`, `#[Middleware]`) leídos por reflexión. No agregues nada a `routes/*.php`
   esperando que aparezca una ruta del proyecto.
2. **Hay 4 puntos de registro y ninguno se deduce de los otros.** Si agregás un controller
   nuevo, revisá los 4 (ver `docs/ARCHITECTURE.md#registration-points`). El más olvidado es
   `routes/tenant.php`: sin él los endpoints existen como DTO pero el router devuelve 404.
3. **Las reglas de negocio mandan sobre el código.** Si `docs/DOMAIN.md` y la
   implementación difieren, el doc es la fuente de verdad: corregí el código o actualizá
   el ADR correspondiente, no cambies el doc en silencio.
4. **El scoping por institución va en un solo lugar.** `users.institution_id` NULL = ve
   todas las instituciones; seteada = solo la suya. No repitas el `where` por consulta:
   un olvido filtra datos entre instituciones y la base no te frena
   ([ADR-0005](docs/decisions/0005-institution-scope-on-platform-users.md)).
5. **Los clientes no tienen scoping**: ven el catálogo completo y compran a cualquier
   institución. `orders.customer_id` y `orders.institution_id` son independientes.

## Domain conventions

- **El código va en inglés**, aunque la especificación de origen esté en español. La
  traducción canónica de cada término está en `docs/DOMAIN.md#ubiquitous-language`; no
  improvises sinónimos (`order`, no `purchase`; `customer`, no `client`).
- **IDs `bigint` autoincrementales** (`$table->id()` / `foreignId()`). Nunca UUID
  ([ADR-0003](docs/decisions/0003-sequential-bigint-identifiers.md)).
- **Todas las tablas llevan `created_at` y `updated_at`**, incluidos los pivotes.
- **No crear tablas de usuarios ni de roles**: los admins son la tabla `users` de la
  plataforma y los roles salen de spatie/laravel-permission
  ([ADR-0004](docs/decisions/0004-reuse-platform-users-for-admins.md)). `customers` sí es
  tabla propia.
- **Los enums son backed enums de PHP** persistidos en columnas `varchar`, no tipos enum de
  PostgreSQL.
- **Montos en CLP como enteros** (`integer` / `bigint`), nunca float ni decimal.

## Conventions

- Usar siempre `Enums\Routes` en vez de strings de ruta sueltos.
- Traducciones bajo el namespace `tickets-valdi::messages.*`; nunca strings hardcodeados
  en Adapters ni Services.
- Servicios en `Services/Model/` envuelven escrituras en `TransactionService`.
- Los repositorios se resuelven vía `RepositoryManager`, registrados en
  `Providers/TicketsValdiServiceProvider.php`.
- Estilo y análisis estático deben quedar limpios **en los archivos que tocás**:
  `./vendor/bin/ecs check app/Projects/TicketsValdi --fix` y
  `./vendor/bin/phpstan analyse app/Projects/TicketsValdi --memory-limit=1G`.
  El resto del repo arrastra ofensas preexistentes; no las arregles en el mismo cambio.

## Known traps

- **No copies `syncRoles()` de SportCompetition.** Ese código nunca recibe el campo `role`
  y rompe PHPStan. Acá se omitió a propósito.
- **`TenantFilterTest` falla en la suite completa y pasa en aislamiento.** Es
  contaminación de estado estático preexistente (`ProjectManager::$currentProject`),
  no la rompiste vos.
- **`testing` es una base SQLite versionada en git** que se modifica al correr tests.
  Restaurala con `git checkout -- testing` antes de commitear.

## Where things are

| Necesitás | Leé |
|---|---|
| Qué es esto y en qué estado está | `docs/README.md` |
| Cómo está construido, puntos de registro, capas | `docs/ARCHITECTURE.md` |
| Reglas de negocio, estados, permisos | `docs/DOMAIN.md` |
| Por qué se decidió algo | `docs/decisions/` |
