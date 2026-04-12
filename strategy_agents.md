# Strategy: Multi-Agent Workflow

> Documento de referencia para el flujo de trabajo colaborativo entre sub-agentes especializados y revisiones humanas.

---

## Visión general

El flujo está compuesto por **cuatro sub-agentes especializados**. Planning y Laravel Specialist operan en secuencia lineal. Validator y Security Agent corren en **paralelo** sobre el mismo PR, con puntos de control humano obligatorios entre fases. Ningún agente reemplaza la decisión humana: la amplifica con contexto técnico y de negocio.

```
                         ┌─────────────────────────────────────────────┐
                         │           CICLO DE DESARROLLO               │
                         └─────────────────────────────────────────────┘

  ┌──────────────┐  ✋ humano  ┌──────────────┐  ✋ humano  ┌──────────────┬──────────────┐
  │  PLANNING    │ ──────────► │   LARAVEL    │ ──────────► │  VALIDATOR   │  SECURITY    │
  │   AGENT      │            │ SPECIALIST   │            │    AGENT     │    AGENT     │
  │  (Kanban)    │            │   AGENT      │            │  (QA + Test) │  (OWASP/MT)  │
  └──────────────┘            └──────────────┘            └──────┬───────┴──────┬───────┘
        ▲                                                         │  paralelo    │
        │                        ✋ humano                        └──────┬───────┘
        └──────────────────────── feedback / ciclo ──────────────────────┘
```

---

## Sub-agente 1 — Planning Agent

### Rol
Gestor de roadmap y contexto de negocio. Mantiene la coherencia histórica del proyecto, prioriza features y define el alcance técnico antes de que se escriba una sola línea de código.

### Conocimiento base
- Tablero Kanban en `.kanban/BOARD.md` y archivos de track en `.kanban/features/**/*.md`
- Tracks históricos: `core-auth`, `language-config`, `timezone-config`, `user-impersonation`
- Proyectos activos: `landlord`, `activities-board`, `sport-competition`, `core`
- Decisiones arquitectónicas registradas en cada track (ej: UTC en BD, locale por usuario con fallback a tenant)
- Sinergia entre features (ej: SF-05 de language-config y timezone-config comparten la misma sección "Configuración General")

### Responsabilidades
1. **Recibir una nueva solicitud** de feature o bugfix
2. **Verificar el historial** de tracks existentes para detectar solapamientos o dependencias
3. **Crear o actualizar** la feature en el board con `/feature add` o `/feature move`
4. **Redactar el archivo de track** con: descripción, análisis arquitectónico, sub-features, estimación de esfuerzo y dependencias
5. **Definir la rama git** sugerida siguiendo la convención: `feat/{track-name}`, `fix/{descripcion}`, `chore/{tarea}`
6. **Producir el brief** que recibirá el Laravel Specialist Agent

### Output hacia el humano (checkpoint #1)
```
📋 Brief de feature #{ID}

Track: {nombre}
Rama sugerida: feat/{nombre}
Sub-features: SF-01, SF-02, SF-03
Dependencias: #{IDs de features relacionadas}
Decisiones previas relevantes: {resumen de track}
Estimación: {N} sub-features de complejidad {baja/media/alta}

¿Aprobamos el scope y la rama? [sí / ajustar]
```

> **Punto de control humano obligatorio:** El humano debe aprobar el scope antes de que el Laravel Specialist Agent comience la implementación. No se abre la rama sin aprobación.

---

## Sub-agente 2 — Laravel Specialist Agent

### Rol
Implementador experto en la arquitectura custom del proyecto. Conoce en profundidad el sistema multi-tenant, el modelo de proyectos, el sistema de rutas por atributos y las capas de abstracción definidas.

### Conocimiento base

#### Arquitectura multi-tenant (`stancl/tenancy`)
- Conexión central vs. conexión por tenant
- `InitializeTenancyByDomain` como middleware base
- El `TENANCY_CENTRAL_CONNECTION` apunta a SQLite en tests
- Config global (`app.timezone`, `app.locale`) **no se modifica por request** — solo se aplica en capa de presentación
- Jobs y queues deben usar UTC explícito en fechas serializadas

#### Sistema de proyectos
- Cada proyecto implementa `ProjectInterface` (`app/Contracts/ProjectInterface.php`)
- `ProjectManager` mapea dominios a clases de proyecto
- Registro en `config/projects.php`

#### Rutas por atributos PHP 8
- `#[Route]`, `#[RoutePrefix]`, `#[Middleware]`, `#[Where]`
- `EndpointProcessor` hace reflection sobre controladores registrados en `config/projects.php`
- Convención: `{projectPrefix}.{classPrefix}.{actionName}` → ej: `landlord.admin.tenants.list`

#### Capas de abstracción
- **Repository**: `BaseRepository` → acceso via `RepositoryManager::get(ModelClass::class)`
- **Service**: lógica de negocio en `Services/Model/`, wrapeada con `TransactionService`
- **Admin CRUD**: `AdminBaseAdapter` + config builders (`ListViewConfig`, `CreateViewConfig`, etc.)
- **FormRequest**: `BaseFormRequest` con `FormBuilder` fluente
- **Guards duales**: `Auth::guard('landlord')` en Landlord, `Auth::guard('web')` en tenants — iteración de guards para obtener usuario activo

#### Reglas críticas de implementación
- `chunk()` **siempre** en operaciones sobre colecciones grandes (seeds, exports, reportes)
- Fechas: guardar en UTC, convertir en display con `->setTimezone()` — nunca `date_default_timezone_set()`
- Helpers globales registrados en `composer.json → autoload.files`
- Middleware de locale/timezone **después** de `InitializeTenancyByDomain` en `bootstrap/app.php`

### Responsabilidades
1. **Leer el brief** del Planning Agent y los archivos de track relacionados
2. **Implementar** siguiendo la estructura de proyectos establecida
3. **Crear migraciones** (central + tenant cuando aplique) con nombres descriptivos
4. **No inventar abstracciones nuevas** sin necesidad — reutilizar las existentes
5. **Documentar decisiones no obvias** con comentarios inline breves
6. **Preparar el PR** con descripción estructurada

### Flujo git durante implementación

```bash
# 1. Partir siempre desde main actualizado
git checkout main && git pull origin main

# 2. Crear rama aprobada por el Planning Agent
git checkout -b feat/{track-name}

# 3. Commits atómicos por sub-feature
git commit -m "feat(SF-01): migración campo locale en users y tenants"
git commit -m "feat(SF-02): middleware SetLocale con fallback tenant → config"
git commit -m "feat(SF-03): sección Configuración General en admin landlord"

# 4. Push y apertura de PR draft
git push -u origin feat/{track-name}
gh pr create --draft --title "feat: {descripción}" --body "..."
```

### Output hacia el humano (checkpoint #2)
```
🔧 Implementación lista para revisión

PR: feat/{track-name} → main (DRAFT)
Sub-features implementadas: SF-01 ✅, SF-02 ✅, SF-03 ✅
Archivos modificados: {lista}
Migraciones nuevas: {lista}
Decisiones tomadas: {resumen de cualquier choice no obvio}

Pendiente de revisión humana antes de marcar como ready.
```

> **Punto de control humano obligatorio:** El humano revisa el PR en draft. Puede solicitar cambios o aprobar para pasar al Validator Agent. El PR **no se marca como ready** sin revisión humana.

---

## Sub-agente 3 — Validator Agent

### Rol
Revisor de calidad técnica. Evalúa el código implementado contra criterios de software engineering y genera un reporte con nota por dimensión. No corrige: diagnostica y justifica.

### Criterios de evaluación

#### Escala de notas
```
5.0  — Ejemplar. Puede usarse como referencia en el proyecto.
4.0  — Sólido. Cumple todos los criterios con mínimas observaciones.
3.0  — Aceptable. Funciona pero tiene deuda técnica identificable.
2.0  — Deficiente. Requiere refactor antes de mergear.
1.0  — Crítico. No debe mergearse en este estado.
```

#### Dimensiones evaluadas

| Dimensión | Peso | Criterio |
|-----------|------|---------|
| **SOLID** | 20% | SRP, OCP, LSP, ISP, DIP aplicados correctamente |
| **Patrones de diseño** | 15% | Uso apropiado de Repository, Service, Adapter, Factory, Strategy |
| **Abstracción y reutilización** | 20% | Extiende correctamente BaseRepository, AdminBaseAdapter, BaseFormRequest, etc. |
| **Manejo de datos masivos** | 15% | `chunk()` en loops sobre colecciones, sin `->all()` sobre tablas grandes |
| **Cobertura de tests** | 20% | Tests unitarios e integración presentes; usan SQLite in-memory; no mockean BD |
| **Convenciones del proyecto** | 10% | Rutas por atributos, guards duales, UTC en BD, naming de rutas |

### Responsabilidades
1. **Leer el PR** y los archivos modificados
2. **Ejecutar el suite de tests** (`php artisan test`) y reportar resultados
3. **Ejecutar análisis estático** (`composer test-phpstan`) y reportar errores
4. **Ejecutar check de estilo** (`composer check-style`) y reportar violaciones
5. **Evaluar cada dimensión** con nota y justificación con referencia a archivo:línea
6. **Emitir nota global** (promedio ponderado)
7. **Listar blockers** (issues que impiden el merge) separados de **observaciones** (mejoras sugeridas)

### Output hacia el humano (checkpoint #3)

```
📊 Reporte de Calidad — feat/{track-name}

Tests:       ✅ 42 passed / ❌ 2 failed
PHPStan:     ✅ No errors
Style:       ⚠️  3 violations (auto-fixable)

─────────────────────────────────────────────
Dimensión                  Nota   Observación
─────────────────────────────────────────────
SOLID                      4.0    SRP respetado. DIP en SetLocale podría
                                  inyectarse vía constructor.
Patrones de diseño         4.5    Repository y Service correctamente usados.
Abstracción y reutilización 3.5   UserFormRequest no extiende BaseFormRequest.
Manejo de datos masivos    5.0    chunk(500) en seed de locales. Correcto.
Cobertura de tests         3.0    SF-03 sin tests de integración.
Convenciones del proyecto  4.0    Rutas por atributos correctas.
─────────────────────────────────────────────
NOTA GLOBAL:               3.9 / 5.0
─────────────────────────────────────────────

🚫 BLOCKERS (deben resolverse antes del merge):
  1. app/Projects/Landlord/FormRequests/UserFormRequest.php:12
     No extiende BaseFormRequest — rompe el contrato del proyecto.
  2. tests/ — SF-03 sin cobertura de integración.

💡 OBSERVACIONES (opcionales, mejoran la nota):
  1. app/Http/Middleware/SetLocale.php:34
     Inyectar TimezoneDisplay vía constructor en lugar de instanciación directa.
```

> **Punto de control humano obligatorio:** El humano revisa el reporte. Decide si los blockers deben resolverse antes del merge o si hay contexto que justifica una excepción. El merge a main **requiere aprobación humana explícita**.

---

## Sub-agente 4 — Security Agent

### Rol
Auditor de seguridad especializado en aplicaciones Laravel multi-tenant. Corre **en paralelo con el Validator Agent** sobre el mismo PR. No implementa correcciones: identifica, clasifica por severidad y justifica. Su reporte es insumo obligatorio para el checkpoint humano final.

### Conocimiento base

#### Amenazas específicas de esta arquitectura multi-tenant
- **Tenant bleeding**: acceso a datos de un tenant desde el contexto de otro. La barrera es `InitializeTenancyByDomain` — cualquier query que bypasee esa inicialización es una fuga.
- **Cross-tenant queries**: modelos del scope central (landlord) que accidentalmente incluyen registros de todos los tenants.
- **Domain spoofing**: manipulación del header `Host` para resolver un tenant distinto al legítimo.
- **Impersonation abuse**: el track `user-impersonation` introduce un vector de escalada de privilegios si no hay logs de auditoría y restricciones de rol.
- **Guard bypass**: el sistema de guards duales (`landlord` / `web`) puede derivar en rutas que responden a ambos guards sin la restricción correcta.

#### OWASP Top 10 en contexto Laravel
| Riesgo OWASP | Vector concreto en este proyecto |
|---|---|
| A01 Broken Access Control | Políticas faltantes en AdminBaseAdapter, rutas sin middleware de rol |
| A02 Cryptographic Failures | Campos sensibles sin cifrado, tokens en logs |
| A03 Injection | Raw queries en Repository sin bindings, `whereRaw` sin sanitizar |
| A04 Insecure Design | Lógica de negocio en controladores en lugar de Services |
| A05 Security Misconfiguration | `APP_DEBUG=true` en producción, headers HTTP sin configurar |
| A06 Vulnerable Components | Dependencias con CVEs conocidos (`composer audit`) |
| A07 Auth Failures | Sesiones sin rotación post-login, remember tokens sin expiración |
| A08 Software Integrity | Assets sin SRI hash, dependencias sin lockfile verificado |
| A09 Logging Failures | Acciones sensibles sin log de auditoría |
| A10 SSRF | Inputs de URL procesados sin whitelist de dominios |

#### Checklist específico para este stack

**Multi-tenancy**
- [ ] Toda ruta de tenant tiene `InitializeTenancyByDomain` antes de cualquier query
- [ ] No hay modelos Eloquent que hagan queries cross-tenant sin scope explícito
- [ ] Las migraciones de tenant no exponen datos de la tabla central `tenants`
- [ ] El subdomain/domain de resolución de tenant no es manipulable por el usuario

**Autenticación y autorización**
- [ ] Cada ruta tiene el middleware de guard correcto (`auth:landlord` o `auth:web`)
- [ ] Las acciones de admin tienen policies o gates, no solo middleware de rol
- [ ] La impersonación de usuario genera un log de auditoría con quién impersonó, a quién y cuándo
- [ ] Las rutas de impersonación requieren rol `super-admin` y no son accesibles por tenants

**Validación e inyección**
- [ ] Todos los inputs pasan por `BaseFormRequest` — sin `$request->all()` directo en controladores
- [ ] No hay `$fillable = ['*']` ni modelos con `unguard()`
- [ ] Raw queries usan bindings (`DB::select('... where id = ?', [$id])`)
- [ ] Uploads de archivos validan tipo MIME real (no solo extensión)

**Exposición de datos**
- [ ] Las respuestas JSON no exponen campos internos (IDs de tenant, tokens, passwords)
- [ ] Los mensajes de error en producción no revelan stack traces ni rutas del filesystem
- [ ] Los logs no persisten datos sensibles (passwords, tokens, datos personales)

**Dependencias**
- [ ] `composer audit` sin vulnerabilidades críticas o altas
- [ ] `npm audit` sin vulnerabilidades críticas en dependencias de frontend

**Headers y configuración**
- [ ] `APP_DEBUG=false` en `.env.production`
- [ ] Headers de seguridad presentes: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`
- [ ] CSRF habilitado en todas las rutas POST/PUT/DELETE (Laravel lo hace por defecto, verificar exclusiones)
- [ ] Cookies de sesión con `secure`, `httponly` y `samesite=lax` configurados

### Responsabilidades
1. **Leer todos los archivos modificados** en el PR
2. **Ejecutar** `composer audit` y `npm audit` y reportar hallazgos
3. **Revisar la inicialización del contexto tenant** en cada nuevo middleware, job o comando artisan
4. **Verificar policies y gates** para toda acción sobre recursos de usuario o tenant
5. **Detectar exposición de datos** en respuestas, logs y mensajes de error
6. **Clasificar hallazgos** por severidad: CRÍTICO / ALTO / MEDIO / BAJO / INFORMATIVO
7. **No sugerir over-engineering** — solo señalar riesgos reales con evidencia en archivo:línea

### Escala de severidad
```
CRÍTICO  — Explotable de forma directa. Bloquea el merge sin excepción posible.
           Ej: tenant bleeding, escalada de privilegios, SQLi confirmado.

ALTO     — Riesgo real pero requiere condiciones adicionales.
           Bloquea el merge salvo aprobación explícita del humano con justificación.
           Ej: ausencia de policy en acción sensible, campo sensible sin cifrar.

MEDIO    — Debilita la postura de seguridad sin impacto inmediato.
           Se registra como deuda de seguridad; no bloquea el merge.
           Ej: header de seguridad faltante, log sin rotación.

BAJO     — Hardening opcional. No bloquea. Se anota en el track.
           Ej: cookie sin SameSite=Strict, rate limiting no configurado.

INFO     — Observación o buena práctica. Sin impacto de riesgo.
```

### Output hacia el humano (checkpoint #3b — paralelo con Validator)

```
🔒 Reporte de Seguridad — feat/{track-name}

composer audit:  ✅ Sin vulnerabilidades
npm audit:       ⚠️  1 vulnerabilidad baja (dev-only)

────────────────────────────────────────────────────────────────────────
Severidad   Archivo                                      Hallazgo
────────────────────────────────────────────────────────────────────────
CRÍTICO     —                                            Sin hallazgos
ALTO        app/Http/Middleware/SetLocale.php:18         Accede a Auth::user() antes
                                                         de que InitializeTenancyByDomain
                                                         haya corrido. En un request de
                                                         tenant, user() podría resolver
                                                         contra la conexión central.
MEDIO       app/Projects/Landlord/Http/Controller/       SettingsController no tiene
            SettingsController.php:45                    policy ni gate — cualquier
                                                         usuario autenticado en landlord
                                                         puede editar la config global.
BAJO        bootstrap/app.php                            Sesión sin SameSite configurado
                                                         explícitamente.
INFO        —                                            chunk() correctamente usado.
                                                         Sin queries cross-tenant detectadas.
────────────────────────────────────────────────────────────────────────

🚫 BLOCKERS DE SEGURIDAD:
  1. [ALTO] SetLocale.php:18 — Riesgo de resolución de usuario contra conexión
     incorrecta. Mover el middleware después de InitializeTenancyByDomain
     en bootstrap/app.php o agregar guard de conexión activa.

💡 DEUDA DE SEGURIDAD (no bloquea):
  1. [MEDIO] Agregar gate 'manage-settings' en SettingsController.
  2. [BAJO] Configurar SESSION_COOKIE_SAMESITE=lax en .env.
```

> **Punto de control humano obligatorio:** El humano revisa ambos reportes (Validator + Security) juntos. Los blockers de seguridad CRÍTICO y ALTO deben resolverse antes del merge, salvo documentación explícita de aceptación de riesgo firmada por el humano.

---

## Flujo completo del ciclo

```
 1. Humano describe la necesidad
       │
       ▼
 2. Planning Agent
    - Revisa historial de tracks y board
    - Detecta solapamientos y dependencias
    - Define scope, sub-features, rama
    - Redacta brief y archivo de track
       │
       ▼
 ✋ 3. CHECKPOINT HUMANO — Aprobación de scope
    ¿Aprobado? ──No──► ajustar scope y volver a 2
       │ Sí
       ▼
 4. Apertura de rama git (feat/{nombre})
       │
       ▼
 5. Laravel Specialist Agent
    - Implementa sub-features en orden
    - Commits atómicos por SF
    - Abre PR en draft
       │
       ▼
 ✋ 6. CHECKPOINT HUMANO — Code review del PR draft
    ¿Aprobado? ──No──► solicita cambios, volver a 5
       │ Sí
       ▼
 7. ┌──────────────────────────────────┬─────────────────────────────────┐
    │  Validator Agent (paralelo)      │  Security Agent (paralelo)      │
    │  - Tests, PHPStan, style         │  - composer audit, npm audit     │
    │  - SOLID, patrones, abstracción  │  - Tenant bleeding, guards       │
    │  - Chunks, tests, convenciones   │  - OWASP Top 10, headers         │
    │  - Reporte con nota y blockers   │  - Reporte con severidad         │
    └──────────────────┬───────────────┴───────────────┬─────────────────┘
                       │                               │
                       └───────────────┬───────────────┘
                                       ▼
 ✋ 8. CHECKPOINT HUMANO — Revisión conjunta de ambos reportes
    ¿Blockers QA o Seguridad CRÍTICO/ALTO? ──Sí──► volver a 5 con contexto
       │ No (o con aceptación documentada de riesgo)
       ▼
 9. PR marcado como ready for review
       │
       ▼
 ✋ 10. CHECKPOINT HUMANO — Aprobación final del PR
        │
        ▼
 11. Merge a main
        │
        ▼
 12. Planning Agent
     - Mueve feature a `done` en el board
     - Actualiza el archivo de track con estado final
     - Registra deuda de seguridad MEDIA/BAJA como nueva feature en backlog
        │
        ▼
 13. Fin del ciclo
```

---

## Reglas generales del sistema

### Lo que los agentes NUNCA hacen sin aprobación humana
- Hacer merge a `main` o `feat/*` estables
- Forzar push (`--force`) sobre cualquier rama
- Eliminar ramas remotas
- Modificar el scope de una feature ya aprobada
- Crear migraciones destructivas (DROP TABLE, DROP COLUMN) sin revisión explícita
- Publicar releases o crear tags
- Ignorar o suprimir hallazgos de seguridad CRÍTICO o ALTO sin documentación humana

### Interacciones entre agentes
- El **Planning Agent** alimenta al **Laravel Specialist** con el archivo de track y el brief
- El **Laravel Specialist** alimenta al **Validator** y al **Security Agent** con la lista de archivos del PR
- El **Validator** y el **Security Agent** corren en paralelo — sus reportes se presentan juntos en el checkpoint #8
- El **Security Agent** alimenta al **Planning Agent** con deuda de seguridad MEDIA/BAJA para registrar en el backlog como features `chore/security`
- El **Validator** alimenta de vuelta al **Planning Agent** con el estado final para cerrar la feature en el board
- Ningún agente tiene acceso de escritura al entorno de producción

### Aceptación documentada de riesgo
Cuando un hallazgo ALTO no puede resolverse antes del merge por razones de negocio, el humano debe documentarlo explícitamente:

```markdown
<!-- En el PR description o en el archivo de track -->
## Riesgo aceptado

| Hallazgo | Severidad | Razón de aceptación | Responsable | Fecha límite de resolución |
|----------|-----------|--------------------|-----------|-----------------------------|
| SettingsController sin gate | ALTO | Feature en MVP, sin acceso externo aún | @humano | 2026-05-01 |
```

Este registro se convierte automáticamente en una feature `chore/security` en el backlog con prioridad `high`.

### Convenciones de branches
| Tipo | Patrón | Ejemplo |
|------|--------|---------|
| Nueva feature | `feat/{track-name}` | `feat/language-config` |
| Bugfix | `fix/{descripcion-corta}` | `fix/locale-middleware-order` |
| Chore / infra | `chore/{descripcion}` | `chore/update-phpstan-config` |
| Hotfix (urgente) | `hotfix/{descripcion}` | `hotfix/tenant-init-crash` |

### Convenciones de commits
Seguir **Conventional Commits** con referencia a sub-feature cuando aplique:
```
feat(SF-01): migración campo locale en users y tenants
feat(SF-02): middleware SetLocale con fallback tenant → config
fix(SF-03): corregir guard dual en SettingsController
test(SF-02): integración de SetLocale en request lifecycle
chore: actualizar .claudeignore con carpeta storage
```

---

## Configuración de los agentes en Claude Code

Cada agente se puede invocar como un sub-agente especializado pasando el rol y contexto correspondiente. El archivo de track activo (`.kanban/features/{track}/*.md`) debe incluirse siempre en el contexto inicial del Laravel Specialist y el Validator.

```
Planning Agent:     /feature board | /feature add | /feature show
Laravel Specialist: Agent(subagent_type=general-purpose) + CLAUDE.md + track file + brief
Validator Agent:    Agent(subagent_type=general-purpose) + archivos modificados + criterios de calidad de este documento
Security Agent:     Agent(subagent_type=general-purpose) + archivos modificados + checklist de seguridad de este documento
```

Los checkpoints humanos se materializan como mensajes de confirmación explícitos antes de que el siguiente agente comience su fase. Si el humano no confirma, el flujo se detiene.

### Contexto mínimo que recibe el Security Agent al invocarse

```
Eres un auditor de seguridad especializado en Laravel con arquitectura multi-tenant (stancl/tenancy).

Proyecto: multi-tenant con proyectos Landlord, ActivitiesBoard, SportCompetition.
Guards: 'landlord' (usuarios centrales) y 'web' (usuarios por tenant).
Tenant init: InitializeTenancyByDomain middleware — toda query de tenant depende de que esto haya corrido.
Track activo: {nombre del track}
Archivos modificados: {lista del PR}

Evalúa usando el checklist y la escala de severidad definidos en strategy_agents.md.
No corrijas el código — diagnostica, clasifica por severidad y referencia archivo:línea.
Separa los BLOCKERS (CRÍTICO/ALTO) de la DEUDA (MEDIO/BAJO/INFO).
```
