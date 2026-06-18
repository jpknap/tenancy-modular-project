# Análisis: migración de TextFilterStrategy a unaccent (PostgreSQL)

## Problema actual

`TextFilterStrategy::applyFilter()` normaliza acentos con REPLACE anidado:

```sql
LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
  REPLACE(REPLACE(REPLACE(REPLACE(
    $column, 'á','a'),'Á','a'),'é','e'),'É','e'),
    'í','i'),'Í','i'),'ó','o'),'Ó','o'),'ú','u'),'Ú','u'
)) LIKE ?
```

Problemas concretos:
- **Cobertura parcial**: solo cubre vocales españolas. Falla con `ü`, `ñ`, `ç`, caracteres de otros idiomas.
- **No usa índices**: la expresión sobre la columna impide que PostgreSQL use índices — full scan en cada consulta.
- **Ilegible y frágil**: agregar un carácter nuevo requiere anidar otro REPLACE.
- **`$column` interpolado directamente en SQL**: seguro en la arquitectura actual (viene del adapter, nunca del usuario), pero es un patrón de riesgo que debe mantenerse controlado.

## Solución: extensión `unaccent` de PostgreSQL

`unaccent` es un módulo estándar de PostgreSQL (incluido en `postgresql-contrib`) que elimina diacríticos de cualquier texto de forma nativa:

```sql
unaccent(LOWER(column)) LIKE unaccent(LOWER(?))
```

Ventajas:
- Cobertura completa de Unicode, no solo español.
- Puede combinarse con índices funcionales (`CREATE INDEX ON table (unaccent(LOWER(column)))`).
- La query queda legible.

---

## Conflicto con el entorno de tests

Los tests corren en SQLite (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`) — `CLAUDE.md`. SQLite no tiene `unaccent`. Si `TextFilterStrategy` lo usa directamente, todos los feature tests que filtran texto rompen.

### Opción A — detección de driver dentro de la estrategia ❌

```php
if (DB::getDriverName() === 'pgsql') {
    return $query->whereRaw("unaccent(LOWER({$column})) LIKE unaccent(?)", [...]);
}
return $query->whereRaw("LOWER({$column}) LIKE ?", [...]);
```

**Problema**: la estrategia conoce detalles del entorno de ejecución. Es una violación de SoC — una clase de dominio no debería ramificar por driver de base de datos. Además, la lógica de producción y la de test conviven en el mismo método.

### Opción B — binding de test en el contenedor ✅ (recomendada)

Mantener `TextFilterStrategy` con lógica única de PostgreSQL. Para tests, registrar en el contenedor una implementación alternativa que use una query compatible con SQLite.

Este enfoque requiere que **el punto D pendiente del análisis principal esté resuelto primero**: `ListColumn::getFilter()` debe usar `app($this->filterStrategy)` en lugar de `new $this->filterStrategy()`. Sin el contenedor, el binding de test no tiene efecto.

---

## Plan de implementación (Opción B)

### Paso 1 — resolver punto D pendiente

```php
// app/Common/Admin/models/ListView/ListColumn.php
public function getFilter(): ?object
{
    if ($this->filterStrategy === null) {
        return null;
    }
    return app($this->filterStrategy);  // era: new $this->filterStrategy()
}
```

Una línea. Sin este cambio el resto no funciona.

### Paso 2 — migración para instalar la extensión

```php
// database/migrations/xxxx_xx_xx_000000_create_unaccent_extension.php
public function up(): void
{
    DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent');
}

public function down(): void
{
    DB::statement('DROP EXTENSION IF EXISTS unaccent');
}
```

`IF NOT EXISTS` hace la migración idempotente — no falla si ya está instalada.

Esta migración solo debe correr en PostgreSQL. Opciones:
- Condicionarla con `if (DB::getDriverName() === 'pgsql')` dentro del `up()`.
- O excluirla del entorno de test via una variable de entorno.

### Paso 3 — actualizar `TextFilterStrategy`

```php
public function applyFilter(Builder $query, string $column, mixed $value): Builder
{
    if (empty($value)) {
        return $query;
    }

    return $query->whereRaw(
        "unaccent(LOWER({$column})) LIKE unaccent(LOWER(?))",
        ['%' . mb_strtolower($value) . '%']
    );
}
```

El método `normalizeText()` y sus `strtr` ya no son necesarios — `unaccent` lo cubre nativamente. Se puede eliminar.

Nota: `$column` sigue interpolado directamente. Mientras la columna provenga siempre del adapter (nunca de input del usuario), es aceptable. Documentar esta invariante en el equipo.

### Paso 4 — crear `SqliteTextFilterStrategy` para tests

```php
// app/Common/Admin/Services/Filters/SqliteTextFilterStrategy.php
namespace App\Common\Admin\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

class SqliteTextFilterStrategy implements FilterStrategyInterface
{
    public function applyFilter(Builder $query, string $column, mixed $value): Builder
    {
        if (empty($value)) {
            return $query;
        }

        return $query->whereRaw(
            "LOWER({$column}) LIKE ?",
            ['%' . mb_strtolower($value) . '%']
        );
    }

    public function getType(): string
    {
        return 'text';
    }
}
```

Esta clase solo existe para tests. No se usa en producción.

### Paso 5 — registrar el binding en el entorno de test

En `tests/TestCase.php` (base de todos los tests del proyecto):

```php
protected function setUp(): void
{
    parent::setUp();

    $this->app->bind(
        \App\Common\Admin\Services\Filters\TextFilterStrategy::class,
        \App\Common\Admin\Services\Filters\SqliteTextFilterStrategy::class,
    );
}
```

Cuando el test llama a `app(TextFilterStrategy::class)`, el contenedor devuelve `SqliteTextFilterStrategy`. El código de producción no sabe que existe esta clase. El adapter, `ListColumn`, `ListViewConfig` y `AdminController` no cambian.

### Paso 6 — índice funcional (opcional, mejora de performance)

Una vez instalado `unaccent`, se puede crear un índice que PostgreSQL use en las consultas de filtrado:

```sql
CREATE INDEX idx_tenants_name_unaccent ON tenants (unaccent(LOWER(name)));
CREATE INDEX idx_users_name_unaccent ON users (unaccent(LOWER(name)));
CREATE INDEX idx_users_email_unaccent ON users (unaccent(LOWER(email)));
```

Esto convierte el filtro de texto de full scan a index scan.

---

## Dependencias entre pasos

```
Punto D (app() en getFilter)
    ↓
Paso 2 (migración unaccent)   Paso 4 (SqliteTextFilterStrategy)
    ↓                               ↓
Paso 3 (TextFilterStrategy         Paso 5 (binding en TestCase)
        con unaccent)
    ↓
Paso 6 (índices, opcional)
```

El paso 1 es bloqueante para todo lo demás. Los pasos 2, 3, 4 y 5 pueden hacerse en paralelo una vez resuelto el punto D.

---

## Resumen de archivos a crear/modificar

| Archivo | Cambio |
|---|---|
| `app/Common/Admin/models/ListView/ListColumn.php` | `new` → `app()` en `getFilter()` |
| `database/migrations/xxxx_create_unaccent_extension.php` | nuevo — instala la extensión en pgsql |
| `app/Common/Admin/Services/Filters/TextFilterStrategy.php` | reemplaza REPLACE anidado por `unaccent`; elimina `normalizeText()` |
| `app/Common/Admin/Services/Filters/SqliteTextFilterStrategy.php` | nuevo — implementación para SQLite en tests |
| `tests/TestCase.php` | bind `TextFilterStrategy` → `SqliteTextFilterStrategy` en `setUp()` |
