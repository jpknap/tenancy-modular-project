---
name: implement
description: Genera un documento detallado de implementación para una feature. Analiza el proyecto y produce un archivo feature_implement_[nombre].md con descripción, plan de acción y plan de implementación técnico.
---

El usuario quiere planificar la implementación de una feature. El argumento recibido es: $ARGUMENTS

## Tu tarea

1. **Interpreta el argumento** como el nombre o descripción de la feature a implementar.
2. **Explora el proyecto** para entender el contexto relevante (arquitectura, archivos relacionados, patrones usados).
3. **Genera el archivo** `.kanban/features/feature_implement_[nombre-en-kebab-case].md` con el contenido detallado.
4. **Confirma** al usuario el archivo creado y un resumen de lo planificado.

## Estructura del archivo a generar

```markdown
# Feature: [Nombre de la feature]

**Proyecto:** [landlord | activities-board | sport-competition | core]
**Prioridad:** [high | medium | low]
**Estado:** draft
**Fecha:** [fecha actual]

---

## Descripción

[Qué es esta feature, qu
é problema resuelve, qué valor aporta]

## Alcance

### Incluye
- [qué abarca esta feature]

### No incluye
- [qué queda fuera explícitamente]

---

## Plan de acción

Pasos de alto nivel ordenados cronológicamente:

1. [ ] Paso 1
2. [ ] Paso 2
3. [ ] Paso 3
...

---

## Plan de implementación técnico

### Archivos a crear
| Archivo | Descripción |
|---------|-------------|
| `app/...` | ... |

### Archivos a modificar
| Archivo | Cambio requerido |
|---------|-----------------|
| `app/...` | ... |

### Migraciones necesarias
- [ ] Migración: [descripción]

### Consideraciones técnicas
- [Dependencias, restricciones, riesgos técnicos]

### Orden de implementación sugerido
1. [Primero esto]
2. [Luego esto]
3. [Finalmente esto]

---

## Criterios de aceptación

- [ ] [Criterio verificable 1]
- [ ] [Criterio verificable 2]

---

## Notas

[Cualquier decisión de diseño, alternativas descartadas, o contexto adicional]
```

## Reglas

- El nombre del archivo usa kebab-case basado en el nombre de la feature (ej: `feature_implement_auth-landlord.md`)
- Crea el directorio `.kanban/features/` si no existe
- Sé específico con los archivos del proyecto real — no uses paths genéricos
- El plan de implementación debe seguir los patrones del proyecto (Repository, Service, Adapter, atributos PHP para rutas)
- Si la feature involucra un nuevo modelo, incluye Repository + Service + Adapter en el plan
