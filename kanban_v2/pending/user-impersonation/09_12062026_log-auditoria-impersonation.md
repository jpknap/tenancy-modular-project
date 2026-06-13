# 09 — Log de auditoría de suplantaciones

**Track:** user-impersonation
**Proyecto:** landlord
**Prioridad:** low
**Estado:** pending

## Qué hacer

### Migración (BD central)
```sql
id, admin_id (FK users central), tenant_id (FK tenants),
target_user_id (int), started_at, ended_at (nullable),
ip, timestamps
```

### Registro automático
- En `ImpersonationService::start()`: crear registro con `started_at = now()`
- En `ImpersonationService::stop()`: actualizar `ended_at = now()`

### Vista en Landlord
- Ruta: `/landlord/admin/impersonation-logs`
- Tabla paginada: admin, tenant, usuario suplantado, inicio, fin, IP
- Solo lectura, sin acciones

## Criterio de aceptación
- Cada suplantación genera un registro en `impersonation_logs`
- La vista es accesible solo para superadmin
- El campo `ended_at` se llena al detener la suplantación
