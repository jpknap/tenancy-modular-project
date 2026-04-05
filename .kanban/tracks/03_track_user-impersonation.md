# Track 03 — Suplantación de Usuario (User Impersonation)

## Descripción
Un administrador Landlord puede suplantar a cualquier usuario de un tenant. El mecanismo usa un **`system_user` oculto** pre-creado en cada tenant como puente de entrada cross-domain. Una vez dentro del tenant como `system_user`, el admin elige al usuario objetivo desde la lista y la sesión cambia a ese usuario. Un header HTTP `X-Impersonating` y un banner visual indican permanentemente que es una sesión de suplantación.

---

## Arquitectura: el enfoque `system_user`

### Por qué este approach es mejor
El problema central de esta arquitectura es que Landlord y Tenant viven en **dominios distintos, guards distintos y schemas de BD distintos**. La solución de token directo obliga a consumir el token en el contexto del tenant — complejo de implementar y difícil de testear.

El enfoque `system_user` divide el problema en **dos pasos independientes y simples**:

```
┌─────────────────────────────────────────────────────────┐
│  PASO 1: Landlord → Tenant (cross-domain)               │
│                                                          │
│  admin.localhost                                         │
│  Admin hace click "Acceder al Tenant"                   │
│    → Genera token de un solo uso (TTL 60s)              │
│    → Redirige a tenant.localhost/system-access/{token}  │
│                                                          │
│  tenant.localhost consume el token                      │
│    → Valida contra BD central (cross-connection)        │
│    → Login como system_user del tenant                  │
│    → Token marcado como usado                           │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│  PASO 2: system_user → Usuario objetivo (mismo dominio) │
│                                                          │
│  tenant.localhost (ya autenticado como system_user)     │
│  Ve la lista de usuarios del tenant                     │
│  Hace click "Suplantar" en un usuario                   │
│    → ImpersonationService::start($target)               │
│    → Auth::login($target) — mismo guard, mismo schema   │
│    → Guarda system_user en sesión para retorno          │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│  PASO 3: Retorno (dos niveles)                          │
│                                                          │
│  "Salir de suplantación" → vuelve a system_user         │
│  "Salir del tenant" → vuelve a admin.localhost          │
└─────────────────────────────────────────────────────────┘
```

---

## El `system_user` en detalle

### Qué es
- Un usuario especial en la tabla `users` del schema de cada tenant.
- Marcado con `is_system_user = true` (campo boolean en migración de tenant).
- **Invisible** para toda la lógica normal: filtrado en todos los queries de usuarios, nunca aparece en listas, no puede hacer login por el formulario normal.
- Sin email real: `system@internal` (o `system@{tenant_id}.internal`).
- Password: UUID v4 auto-generado, almacenado **hasheado en el schema del tenant** y en texto encriptado en la BD central (tabla `tenant_system_accounts`).

### Cuándo se crea
Al crear un nuevo Tenant, el `TenantService::create()` dispara la creación del `system_user` en el schema del tenant recién creado. Se guarda la contraseña encriptada en central para que el Landlord pueda generar el token de acceso.

### Qué ve el system_user dentro del tenant
Solo ve la pantalla de "Seleccionar usuario para suplantar" — una vista reducida de la lista de usuarios del tenant con el botón "Suplantar". No tiene acceso a ninguna otra sección. Esto se controla con un middleware `EnsureIsSystemUser` que redirige cualquier otra ruta.

---

## Librerías

| Librería | Uso | Recomendación |
|----------|-----|---------------|
| Implementación manual | Control total sobre flujo cross-tenant | ✅ Necesario, la arquitectura es custom |
| `lab404/laravel-impersonate` | NO aplica | ❌ Asume mismo guard y misma sesión |
| `illuminate/encryption` (nativo) | Encriptar password del system_user en BD central | ✅ Ya incluido |

---

## Consideraciones críticas

### Seguridad
1. **system_user no es loginable** desde el formulario normal — el Middleware de login debe rechazar si `is_system_user = true`.
2. **Token de acceso**: un solo uso, TTL 60 segundos, almacenado con hash en BD central. Si expira → redirect a admin.localhost con mensaje de error.
3. **Quién puede generar el token**: solo usuarios Landlord con permiso `can_impersonate`.
4. **No suplantar a system_user**: en la lista de suplantación, filtrar `is_system_user = true`.
5. **Acciones bloqueadas durante suplantación**: cambiar password, eliminar cuenta. Un middleware `BlockedDuringImpersonation` protege estas rutas.

### Header HTTP
- Middleware `ImpersonationHeaders` agrega en cada response durante suplantación:
  - `X-Impersonating: true`
  - `X-Impersonated-By: system` (indica que es sesión de soporte)
  - `X-Impersonation-Admin: {admin_id}` (id del admin Landlord original)

### Sesión y retorno
- Al entrar al tenant como system_user: `session(['system_entry' => true, 'landlord_admin_id' => $adminId])`
- Al iniciar suplantación: `session(['impersonating_user_id' => $target->id, 'impersonated_as' => $target->name])`
- Botón "Salir de suplantación" → vuelve a system_user (Auth::login($systemUser))
- Botón "Salir del tenant" → invalida sesión + redirect a `admin.localhost`

### Filtrado del system_user en queries
- En `BaseRepository::paginate()` y `all()`, aplicar scope global si el modelo tiene `HasSystemUser`.
- O mejor: scope en el modelo User del tenant: `scopeExcludeSystem($q) { $q->where('is_system_user', false); }`
- Este scope se aplica automáticamente en todos los queries de UserAdmin.

---

## Plan de implementación — Sub-features

### SF-01 · Migración: `is_system_user` en users del tenant
- Migración tenant: `ALTER TABLE users ADD is_system_user BOOLEAN DEFAULT FALSE`
- Scope en modelo User del tenant para excluirlo de queries normales

### SF-02 · Migración: `tenant_system_accounts` en BD central
```sql
id, tenant_id (FK tenants), encrypted_password, 
access_token (nullable, hashed), token_expires_at (nullable),
created_at, updated_at
```

### SF-03 · Creación automática del `system_user` al crear Tenant
- En `TenantService::create()`, después de crear el tenant y correr sus migraciones:
  - Conectar al schema del tenant
  - Crear user con `is_system_user = true`, email `system@internal`, password aleatorio
  - Guardar password encriptado en `tenant_system_accounts`

### SF-04 · Botón "Acceder al Tenant" en lista de tenants (Landlord)
- En `TenantAdmin::getListViewConfig()`, acción por fila "Acceder"
- POST a `/landlord/impersonation/enter/{tenantId}`
- `ImpersonationService::generateAccessToken($tenant)` → guarda token hasheado, redirige

### SF-05 · Endpoint de consumo del token en Tenant
- Ruta en `routes/tenant.php` (registrada manualmente, fuera del EndpointProcessor):
  `GET /system-access/{token}`
- Valida token contra BD central (cross-connection `pgsql` explícita)
- Login como system_user → redirige a pantalla de selección de usuario

### SF-06 · Vista de selección de usuario para suplantar
- Controlador `SystemImpersonationController` (solo accesible para system_user)
- Lista usuarios del tenant (excluyendo system_user)
- Botón "Suplantar" por usuario → POST `/system/impersonate/{userId}`
- Middleware `EnsureIsSystemUser` protege este controlador

### SF-07 · `ImpersonationService::start/stop` (dentro del tenant)
- `start(User $target)`: valida que no sea system_user, Auth::login($target), guarda en sesión
- `stop()`: Auth::login($systemUser), limpia sesión de suplantación

### SF-08 · Middleware `ImpersonationHeaders`
- Detecta `session('impersonating_user_id')` o `session('system_entry')`
- Agrega headers correspondientes en cada response

### SF-09 · Banner visual en layout del tenant
- En `layouts/layout_menu_sidebar.blade.php`:
  - Si `session('impersonating_user_id')`: banner naranja con usuario y botón "Salir de suplantación"
  - Si `session('system_entry')` (solo como system_user): banner diferente "Modo soporte — selecciona usuario"

### SF-10 · Tabla `impersonation_logs` y registro de auditoría
```sql
-- BD central
id, admin_id, tenant_id, target_user_id, 
started_at, ended_at, ip, timestamps
```
- Registrar en SF-07 al iniciar/terminar suplantación
- Vista de logs en `/landlord/admin/impersonation-logs`

---

## Estimación de esfuerzo

| Sub-feature | Complejidad | Dependencias |
|------------|-------------|--------------|
| SF-01 Campo is_system_user | Baja | Ninguna |
| SF-02 Tabla system_accounts | Baja | Ninguna |
| SF-03 Crear system_user en TenantService | Media | SF-01, SF-02 |
| SF-04 Botón en lista tenants | Baja | SF-02, SF-03 |
| SF-05 Endpoint consumo token | Media | SF-02, SF-04 |
| SF-06 Vista selección usuario | Media | SF-01, SF-05 |
| SF-07 ImpersonationService | Media | SF-01, SF-06 |
| SF-08 Middleware headers | Baja | SF-07 |
| SF-09 Banner visual | Baja | SF-07 |
| SF-10 Logs auditoría | Media | SF-07 |

**Total: 10 sub-features** — pero cada una es acotada y bien delimitada.

### MVP recomendado (primera iteración)
SF-01 → SF-02 → SF-03 → SF-04 → SF-05 → SF-06 → SF-07 → SF-09
Deja SF-08 (headers) y SF-10 (logs) para segunda iteración. Esto entrega el flujo completo funcional.
