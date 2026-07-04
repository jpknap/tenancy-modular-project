# 04 — AdminController: manejo multipart en create/edit

**Track:** file-upload-service
**Proyecto:** core (Common)
**Prioridad:** medium
**Estado:** pending

## Objetivo
- Detectar `UploadedFile` en los datos validados de create/edit
- Delegar a `FileUploaderInterface::upload()` (binding ya registrado por proyecto)
- Persistir `path`/`url` del `FileUploadResult` en el modelo vía el service de dominio
- Considerar limpieza del archivo si la transacción DB hace rollback (deuda anticipada del track — `TransactionService::executeWithRollbackHandler` ya existe)

## Dependencias
- SF-03 (campo file en FormBuilder) — sin él no llegan archivos al controller
