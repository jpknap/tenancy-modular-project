# ADR-0005: Un solo schema y alcance por `users.institution_id` nullable

- **Status**: Accepted
- **Date**: 2026-09-21
- **Deciders**: JuanMunozBouchard

## Context

El modelo de origen resolvía la relación usuario↔institución con un pivote N:M
(`admin_institucion`), y dejaba abierto si cada institución debía vivir aislada.

Dos requisitos del negocio cierran la discusión:

1. **Los clientes deben ver todas las instituciones.** El front público es un catálogo
   común: un comprador navega y compra entradas de cualquier institución. Separar
   instituciones en schemas distintos obligaría a consultar N conexiones para armar un
   listado.
2. **Las compras vinculan clientes con instituciones ajenas.** Un cliente no pertenece a
   ninguna institución; solo le compra. La relación es transaccional, no de pertenencia.
3. **Un usuario del panel pertenece a una institución**, y los admins del landlord deben
   ver todo.

La plataforma ya aísla por *tenant* mediante schemas de PostgreSQL. Agregar una segunda
dimensión de aislamiento físico por institución duplicaría ese mecanismo sin necesidad.

## Decision

1. **Todas las instituciones de un tenant comparten el mismo schema.** El aislamiento por
   institución es lógico (filtros de aplicación), no físico.
2. **El pivote `admin_institucion` no se crea.** En su lugar, una migración del proyecto
   agrega a `users` una FK `institution_id` **nullable**:
   - `NULL` → admin del landlord / `super_admin`: ve y opera todas las instituciones.
   - seteada → el usuario opera únicamente esa institución.
3. **Los clientes no tienen alcance por institución.** `customers` ve el catálogo completo.

## Consequences

### Positive

- El catálogo público es una consulta simple sobre una sola conexión.
- El alcance de un usuario se lee de una columna: no hace falta join ni cargar una
  colección de instituciones para decidir qué puede ver.
- `NULL` como "ve todo" hace que un usuario recién creado sin institución sea, por defecto,
  global — coherente con cómo se dan de alta los admins del sistema.

### Negative

- **Un usuario no puede operar dos instituciones** sin ser global. Si el negocio lo pide,
  hay que volver al pivote; la migración de vuelta es simple pero toca todas las consultas.
- **El aislamiento depende de la aplicación**: un `where` olvidado filtra datos entre
  instituciones. Un bug de scoping no lo frena la base de datos, a diferencia del
  aislamiento por schema entre tenants.
- Se modifica una tabla de la plataforma (`users`) desde una migración del proyecto, lo que
  acopla el proyecto al esquema común.

### Follow-up

- Implementar el scoping en un solo lugar — global scope de Eloquent o método base en los
  repositorios — y **no** repetir el `where` en cada consulta. Es la mitigación principal
  del riesgo de arriba.
- Tests que verifiquen que un usuario de la institución A no ve datos de la B.
- La columna `is_system_user`, que ya existe en `users`, cumple un rol parecido para la
  suplantación: revisar que ambos conceptos no se pisen al implementar permisos.

## Alternatives considered

| Alternativa | Por qué se descartó |
|---|---|
| Pivote `institution_user` N:M (modelo original) | El negocio no pide multi-institución por usuario; suma un join a cada consulta de scoping |
| Un schema por institución | Duplica el mecanismo de tenancy y rompe el catálogo público unificado, que es el requisito central |
| Columna `is_global` booleana además de la FK | Redundante: `institution_id IS NULL` ya expresa exactamente eso, sin riesgo de estados contradictorios |
