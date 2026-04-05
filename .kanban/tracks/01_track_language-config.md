# Track 01 — Configuración de Idioma por Tenant/Usuario

## Descripción
Permitir que cada tenant (o usuario) configure el idioma de la aplicación desde un panel de "Configuración General". Laravel aplica el locale en cada request antes de renderizar cualquier vista.

---

## Análisis arquitectónico

### Decisión clave: ¿idioma por tenant o por usuario?
En esta arquitectura multi-tenant el nivel correcto es **por usuario**, con un fallback al tenant. Razón: distintos usuarios de un mismo tenant pueden preferir idiomas distintos. El tenant define el idioma por defecto, el usuario puede sobreescribirlo.

### Dónde almacenar
- **Tenant**: campo `locale` en tabla `tenants` (central) — idioma predeterminado del tenant.
- **Usuario**: campo `locale` en tabla `users` (tanto central como por-tenant) — override personal.
- **No** usar el campo `data` JSON del Tenant para esto: es opaco, no indexable y complica las queries.

### Flujo de aplicación
```
Request entra
  → Middleware SetLocale
      → toma Auth::user()->locale
      → si null → toma tenant->locale
      → si null → config('app.locale') (fallback global)
      → App::setLocale($locale)
  → Controller renderiza vista ya con el locale activo
```

---

## Librerías

| Librería | Uso | Necesaria |
|----------|-----|-----------|
| `laravel/lang` (nativo) | `App::setLocale()`, archivos `lang/es/`, `lang/en/` | ✅ Ya incluido |
| `spatie/laravel-translatable` | Para traduccir contenido de BD (actividades, etc.) | Opcional, fase 2 |
| `mcamara/laravel-localization` | Routing por locale (`/es/admin`, `/en/admin`) | ❌ No aplica aquí |

**Conclusión:** Solo se necesita el soporte nativo de Laravel + archivos de traducción.

---

## Consideraciones críticas

1. **UTC en BD siempre** — el locale no afecta fechas, solo strings de UI.
2. **Blade cache** — al cambiar locale mid-session, limpiar `php artisan view:clear` en deploy.
3. **JS/Alpine locale** — si hay componentes JS con fechas/números, deben recibir el locale desde una variable PHP (`@js(['locale' => app()->getLocale()])`).
4. **Traducciones de validación** — los mensajes de error de FormRequest usan `lang/es/validation.php`. Asegurarse de copiar los archivos de Laravel lang.
5. **Multi-tenant**: el Middleware debe ejecutarse DESPUÉS de `InitializeTenancyByDomain` para poder leer el tenant activo.
6. **Guard dual**: en Landlord el usuario es `Auth::guard('landlord')->user()`, en Tenant es `Auth::guard('web')->user()`. El Middleware debe ser consciente de esto (usar el mismo patrón de iterar guards que se estableció en ProfileFormRequest).

---

## Plan de implementación — Sub-features

### SF-01 · Migración: campo `locale` en users y tenants
- Migración central: `ALTER TABLE users ADD locale VARCHAR(10) NULL`
- Migración tenant: igual en schema de cada tenant
- Migración central: `ALTER TABLE tenants ADD locale VARCHAR(10) NULL DEFAULT 'es'`

### SF-02 · Middleware `SetLocale`
- Crear `app/Http/Middleware/SetLocale.php`
- Registrar en `bootstrap/app.php` en el grupo `web`, después de `InitializeTenancyByDomain`
- Lógica: usuario → tenant → config fallback

### SF-03 · Sección "Configuración General" en Admin
- Nuevo `SettingsController` (no usa AdminBaseAdapter — no es un CRUD de modelo, es un formulario de config)
- Ruta: `GET/POST /landlord/settings/general`
- FormRequest `GeneralSettingsFormRequest`
- Vista nueva o reutilizar `landlord/edit.blade.php`

### SF-04 · Campo locale en formulario de usuario
- Agregar `->select('locale', 'Idioma', ['es' => 'Español', 'en' => 'English'])` en `UserFormRequest` de cada proyecto
- Actualizar `UserService::update()` para persistir el campo

### SF-05 · Archivos de traducción
- Publicar `php artisan lang:publish`
- Traducir: `lang/es/` y `lang/en/` para validation, auth, pagination
- Crear `lang/es/app.php` y `lang/en/app.php` para strings propios de la UI

### SF-06 · Selector rápido en topbar (opcional, fase 2)
- Dropdown en `top-bar.blade.php` para cambiar locale sin ir a configuración
- POST a ruta `/profile/locale` que guarda en `users.locale`

---

## Estimación de esfuerzo
| Sub-feature | Complejidad | Dependencias |
|------------|-------------|--------------|
| SF-01 Migración | Baja | Ninguna |
| SF-02 Middleware | Baja | SF-01 |
| SF-03 Sección Settings | Media | SF-02 |
| SF-04 Campo en User form | Baja | SF-01 |
| SF-05 Archivos traducción | Alta (contenido) | SF-02 |
| SF-06 Selector topbar | Baja | SF-01, SF-02 |

**Total: 4–6 features pequeñas** (sin contar SF-06)
