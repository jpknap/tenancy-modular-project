# 03 — Botón "Suplantar" en lista de usuarios

**Track:** impersonation
**Proyecto:** sport-competition, landlord
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `UserAdmin` de SportCompetition y Landlord actualizado con nueva acción:
  - Label: `Suplantar`
  - Icono: `bi-person-fill-gear text-warning`
  - Tipo: `form` con `form_method: 'POST'`
  - `permission: 'users:impersonate'` → solo visible para quienes tienen este permiso
  - Confirmación antes de ejecutar

## Criterio de aceptación
- Superadmin ve el botón (icono amarillo de persona con engranaje) junto a Editar y Eliminar
- Admin sin `users:impersonate` no ve el botón
- Click muestra confirm dialog antes de enviar el formulario
