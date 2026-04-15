# System Login Flow - Antes y Después del Fix

## ANTES (Con Ciclo Infinito) ❌

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. Landlord: Superadmin accede a /landlord/admin/tenants        │
│    → Click "Acceso al Sistema" → Genera token                   │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. Redirige a: /sport-competition/system-login?uid=X&exp=Y&sig=Z│
│    Host: sport-competition.localhost (Tenant)                   │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. Middleware Stack (Tenant):                                   │
│    - web                                                        │
│    - LogTenancyState ANTES                                      │
│    - InitializeTenancyByDomain ← Tenancia del Tenant            │
│    - PreventAccessFromCentralDomains                            │
│    - LogTenancyState DESPUÉS                                    │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. SystemLoginController::login()                               │
│    ✅ Valida firma                                               │
│    ✅ Encuentra usuario (id=1)                                   │
│    ✅ Auth::guard('web')->loginUsingId(1)                       │
│    ✅ Sesión creada: [auth.web] = User#1                        │
│    ✅ Obtiene proyecto: SportCompetitionProject                 │
│    ✅ Redirige a: /sport-competition/admin/users/list           │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. GET /sport-competition/admin/users/list                      │
│    Middleware Stack:                                            │
│    - web                                                        │
│    - LogTenancyState ANTES                                      │
│    - InitializeTenancyByDomain ← Tenancia del Tenant            │
│    - PreventAccessFromCentralDomains                            │
│    - ProjectInitialized                                         │
│    - LogTenancyState DESPUÉS                                    │
│    - auth.landlord ← ❌ PROBLEMA                                 │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. EnsureAuthenticated::handle(guard='landlord')                │
│    Verificar: Auth::guard('landlord')->check()                 │
│    ❌ NO ENCONTRADO (usuario está en guard='web')               │
│    ✅ Sesión existe: [auth.web] = User#1                        │
│    ❌ Sesión no contiene: [auth.landlord]                       │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 7. Redirige a login: /sport-competition/auth/login              │
│    ← VUELVE AL INICIO DEL CICLO                                 │
│    ↺ ↺ ↺ CICLO INFINITO ↺ ↺ ↺                                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## DESPUÉS (Fix Aplicado) ✅

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. Landlord: Superadmin accede a /landlord/admin/tenants        │
│    → Click "Acceso al Sistema" → Genera token                   │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. Redirige a: /sport-competition/system-login?uid=X&exp=Y&sig=Z│
│    Host: sport-competition.localhost (Tenant)                   │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. Middleware Stack (Tenant):                                   │
│    - web                                                        │
│    - LogTenancyState ANTES                                      │
│    - InitializeTenancyByDomain ← Tenancia del Tenant            │
│    - PreventAccessFromCentralDomains                            │
│    - LogTenancyState DESPUÉS                                    │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. SystemLoginController::login()                               │
│    ✅ Valida firma                                               │
│    ✅ Encuentra usuario (id=1)                                   │
│    ✅ Auth::guard('web')->loginUsingId(1)                       │
│    ✅ Sesión creada: [auth.web] = User#1                        │
│    ✅ Obtiene proyecto: SportCompetitionProject                 │
│    ✅ Redirige a: /sport-competition/admin/users/list           │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. GET /sport-competition/admin/users/list                      │
│    Middleware Stack:                                            │
│    - web                                                        │
│    - LogTenancyState ANTES                                      │
│    - InitializeTenancyByDomain ← Tenancia del Tenant            │
│    - PreventAccessFromCentralDomains                            │
│    - ProjectInitialized                                         │
│    - LogTenancyState DESPUÉS                                    │
│    - auth.web ← ✅ CORRECTO (cambio del fix)                     │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. EnsureAuthenticated::handle(guard='web')                     │
│    Verificar: Auth::guard('web')->check()                      │
│    ✅ ENCONTRADO (usuario está en guard='web')                   │
│    ✅ Sesión contiene: [auth.web] = User#1                      │
└─────────────────────────────────────────────────────────────────┘
                                 ↓
┌─────────────────────────────────────────────────────────────────┐
│ 7. AdminController::list()                                      │
│    ✅ Usuario autenticado                                        │
│    ✅ Renderiza listado de usuarios                              │
│    ✅ FIN EXITOSO                                                │
└─────────────────────────────────────────────────────────────────┘
```

---

## Comparación de Guards

### Guard `landlord`
```
Contexto: Landlord (sin tenancia inicializada)
Provider: landlord_users
Modelo: App\Models\User
Conexión: default (landlord)
Sesión: [auth.landlord] = User#X

⚠️ Problema en Multi-tenancia:
- En Tenant, la conexión se cambia a la del tenant
- Pero el guard 'landlord' sigue apuntando a la conexión original
- El usuario no se encuentra (está en otra BD)
```

### Guard `web` (Correcto para Multi-tenancia)
```
Contexto: Landlord (sin tenancia) o Tenant (con tenancia)
Provider: users  
Modelo: App\Models\User
Conexión: default (cambia automáticamente por middleware de tenancia)
Sesión: [auth.web] = User#X

✅ Funciona en ambos contextos:
- Sin tenancia: usa conexión del landlord
- Con tenancia: usa conexión del tenant (cambiada por middleware)
- El modelo User siempre apunta a la conexión correcta
```

---

## Timeline de la Solución

### Problema Inicial
- Logs mostraban ciclo de redirecciones
- Tiempos de ~500ms por ciclo

### Instrumentación de Logs
- Agregados 6 componentes con logs detallados
- Prefix `[Log-System-Auth]` para fácil filtrado

### Diagnóstico
- Logs revelaron: `guard='landlord'` pero `web_check=true`
- Root cause: Admin requería `auth.landlord`, usuario estaba en `auth.web`

### Fix
- Cambio de 1 línea en `AdminController`
- De: `#[Middleware(['auth.landlord'])]`
- A: `#[Middleware(['auth.web'])]`

### Resultado
- Sistema login funciona correctamente
- Ciclo infinito resuelto
- Logs permanecen para debugging futuro
