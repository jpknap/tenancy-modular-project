# Track 02 — Configuración de Zona Horaria

## Descripción
La base de datos y el sistema siempre operan en **UTC**. Cada tenant o usuario configura su zona horaria, y toda fecha mostrada al usuario se convierte a esa zona en capa de presentación, nunca antes.

---

## Análisis arquitectónico

### Regla de oro: UTC en BD, timezone en display
```
Insertar:  Carbon::now('UTC') → se guarda como UTC en BD
Mostrar:   $date->setTimezone($userTimezone)->format('d/m/Y H:i')
```
No hay excepciones. Toda lógica de negocio (comparaciones, sumas de días, etc.) opera en UTC.

### Dónde almacenar
- **Tenant**: campo `timezone` en tabla `tenants` (central) — zona horaria predeterminada del tenant.
- **Usuario**: campo `timezone` en tabla `users` — override personal.
- Misma estrategia que locale: usuario → tenant → `config('app.timezone')` (default UTC).

### Riesgo específico de esta arquitectura
`stancl/tenancy` inicializa la conexión de BD al inicio del request. Si `config('app.timezone')` cambia a mitad de request puede haber inconsistencias con timestamps de Eloquent. La solución es setear la timezone **solo para presentación**, sin tocar `date_default_timezone_set()` globalmente.

---

## Librerías

| Librería | Uso | Recomendación |
|----------|-----|---------------|
| `Carbon` (nativo) | Conversión y formateo de fechas con timezone | ✅ Ya incluido en Laravel |
| `nesbot/carbon` | Ya es Carbon, no instalar aparte | ✅ |
| `jamesmills/laravel-timezone` | Detección automática de timezone por IP + UI | Opcional, buena UX |
| Lista de timezones | `timezone_identifiers_list()` PHP nativo | ✅ Nativo, ~400 zonas |

**Conclusión:** Carbon nativo es suficiente. `jamesmills/laravel-timezone` agrega valor en UX pero no es crítico.

---

## Consideraciones críticas

1. **NUNCA llamar `date_default_timezone_set()` en un Middleware** — afecta Carbon globalmente y rompe timestamps de Eloquent (created_at, updated_at). En su lugar usar `->setTimezone()` en capa de presentación.

2. **Blade helper recomendado** — crear un helper `display_date($date, $format)` que automáticamente aplique la timezone del usuario activo:
   ```php
   function display_date(Carbon $date, string $format = 'd/m/Y H:i'): string {
       $tz = auth()->user()?->timezone ?? config('app.timezone');
       return $date->copy()->setTimezone($tz)->format($format);
   }
   ```

3. **Formularios con datetime inputs** — los `<input type="datetime-local">` del browser envían en local time del browser, NO en UTC. Al recibir en el backend, convertir siempre a UTC antes de guardar:
   ```php
   Carbon::createFromFormat('Y-m-d\TH:i', $request->datetime, $userTimezone)->utc()
   ```

4. **Migraciones de BD** — PostgreSQL con columnas `TIMESTAMP WITH TIME ZONE` almacena en UTC internamente. Verificar que las columnas existentes usen `timestamps()` en migraciones (que es TIMESTAMP sin timezone en PG). Consistente con guardar UTC explícitamente desde la app.

5. **Queue Jobs** — los jobs serializan Carbon. Si un job se crea en timezone de usuario y se ejecuta en otro contexto puede fallar. Toda fecha en jobs debe ser UTC explícito.

6. **Stancl/tenancy + config cache** — `config('app.timezone')` es global. No modificarlo por request. El timezone del usuario es solo para `->setTimezone()` en display.

---

## Plan de implementación — Sub-features

### SF-01 · Migración: campo `timezone` en users y tenants
- Migración central: `ALTER TABLE users ADD timezone VARCHAR(50) NULL`
- Migración tenant: igual en schema de cada tenant
- Migración central: `ALTER TABLE tenants ADD timezone VARCHAR(50) NULL DEFAULT 'UTC'`

### SF-02 · Helper/Service `TimezoneDisplay`
- Crear `app/Common/Services/TimezoneDisplay.php` (o helper global en `app/helpers.php`)
- Método: `display(Carbon|string $date, string $format): string`
- Resuelve timezone del usuario activo automáticamente (iterando guards)
- Registrar helper en `composer.json` → `autoload.files`

### SF-03 · Blade directive `@displayDate`
- Registrar en `AppServiceProvider`:
  ```php
  Blade::directive('displayDate', fn($expr) => "<?= display_date($expr) ?>");
  ```
- Uso en vistas: `@displayDate($activity->created_at, 'd/m/Y H:i')`

### SF-04 · Campo timezone en formulario de usuario y settings de tenant
- Select con `timezone_identifiers_list()` agrupado por región
- Agregar en `UserFormRequest` de cada proyecto
- Agregar en `GeneralSettingsFormRequest` del tenant (comparte infraestructura con Feature 1)

### SF-05 · Sección "Configuración General" en Admin (compartida con Feature 1)
- Si Feature 1 ya creó esta sección, agregar el campo timezone ahí
- Si se implementa solo Feature 2, crear la sección igual

### SF-06 · Input UTC en creación/edición de entidades con fechas
- Revisar `ActivityFormRequest` y similares donde hay `date` o `datetime`
- Agregar conversión UTC en `ActivityService::create/update`

---

## Estimación de esfuerzo
| Sub-feature | Complejidad | Dependencias |
|------------|-------------|--------------|
| SF-01 Migración | Baja | Ninguna |
| SF-02 Helper display | Baja-Media | SF-01 |
| SF-03 Blade directive | Baja | SF-02 |
| SF-04 Campo en formularios | Baja | SF-01 |
| SF-05 Sección Settings | Media | SF-01 (o Feature 1 SF-03) |
| SF-06 Input UTC en entidades | Media | SF-02 |

**Total: 4–6 sub-features**

### Sinergia con Feature 1
SF-05 es **exactamente la misma sección** que crea Feature 1. Implementar ambas juntas ahorra una iteración completa. Se recomienda implementar Feature 1 + Feature 2 como un bloque "Configuración General".
