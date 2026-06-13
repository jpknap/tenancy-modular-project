# Kanban v2

Estructura reorganizada el 12/06/2026. El `.kanban/` original se mantiene intacto como referencia.

## Estructura

```
kanban_v2/
├── progress/{track}/     ← features activamente en desarrollo
├── pending/{track}/      ← features planificadas pero no iniciadas
├── done/{track}.md       ← resumen de features completadas por track
└── tracks/{track}.md     ← arquitectura, decisiones y contexto del track
```

## Estado de tracks

| Track | Estado | Carpeta |
|-------|--------|---------|
| `core-auth` | ✅ done | `done/core-auth.md` |
| `language-config` | ✅ done | `done/language-config.md` |
| `timezone-config` | ✅ done | `done/timezone-config.md` |
| `user-impersonation` | ✅ done (SF-10 pending) | `done/user-impersonation.md` |
| `admin-list-column-filters` | ⚠️ parcial | `done/` + `pending/` |
| `permission-system` | 🔄 in-progress | `progress/permission-system/` |
| `audit-system` | ⏳ pending | `pending/audit-system/` |
| `restify-api` | ⏳ pending | `pending/restify-api/` |

## Convención de nombres de features

```
{número}_{ddmmyyyy}_{nombre-kebab-case}.md

Ejemplo:
  01_12062026_instalar-spatie-permission.md
```

## Próximo paso recomendado

Completar `progress/permission-system/` (SF-01 a SF-05) antes de arrancar cualquier otro track.
Ver `tracks/permission-system.md` para el contexto completo.
