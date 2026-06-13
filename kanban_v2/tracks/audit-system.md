# Track — Sistema de Auditoría (audit-system)

## Estado actual
**Pendiente** — no iniciado.

## Descripción
Registrar automáticamente todos los cambios a modelos Eloquent usando `owen-it/laravel-auditing`. Cada tenant tiene su propia tabla `audits` en su schema. La BD central tiene su propia tabla `audits` para trackear cambios a `Tenant`. Un panel de UI permite revisar el historial de cambios.

---

## Arquitectura

### Aislamiento por tenant
Con `stancl/tenancy` cada schema de tenant tiene su propia tabla `audits` — sin contaminación cross-tenant. La configuración `connection = null` en `config/audit.php` hace que owen-it herede la conexión activa del tenant.

### UserResolver dinámico
El resolver nativo de owen-it usa `Auth::user()`. Esta arquitectura tiene dos guards (`landlord` y `web`), por eso se necesita un `UserResolver` personalizado que detecte el contexto.

```php
class UserResolver implements \OwenIt\Auditing\Contracts\UserResolver
{
    public static function resolve(): ?Authenticatable
    {
        return tenancy()->initialized
            ? auth('web')->user()
            : auth('landlord')->user();
    }
}
```

### Modelos a auditar
| Modelo | Schema | Archivo |
|--------|--------|---------|
| `User` (central) | BD central | `app/Models/User.php` |
| `Tenant` | BD central | `app/Models/Tenant.php` |
| `User` (tenant) | Schema tenant | `*/Projects/*/Models/User.php` |
| `Activity` | Schema tenant | `app/Projects/ActivitiesBoard/Models/Activity.php` |
| `Competition`, `Team`, `Player`, `GameMatch` | Schema tenant | `app/Projects/SportCompetition/Models/` |

---

## Sub-features

| # | Feature | Archivo | Prioridad |
|---|---------|---------|-----------|
| #030 | Instalar owen-it + config | `pending/audit-system/01_*` | high |
| #031 | UserResolver guard dinámico | `pending/audit-system/02_*` | high |
| #032 | Migration audits tenant | `pending/audit-system/03_*` | high |
| #033 | Migration audits central | `pending/audit-system/04_*` | high |
| #034 | Auditable en modelos centrales | `pending/audit-system/05_*` | high |
| #035 | Auditable en modelos tenant | `pending/audit-system/06_*` | high |
| #036 | Modelos SportCompetition | `pending/audit-system/07_*` | medium |
| #037 | UI Audit panel tenant | `pending/audit-system/08_*` | medium |
| #038 | UI Audit cross-tenant landlord | `pending/audit-system/09_*` | medium |
| #039 | Comando audit:prune + schedule | `pending/audit-system/10_*` | low |

## MVP recomendado
#030 → #031 → #032 → #033 → #034 → #035 → #037
Deja SportCompetition (#036), UI landlord (#038) y prune (#039) para segunda iteración.
