# 05 — AttachmentUploadService (implementación concreta en ActivitiesBoard)

**Track:** file-upload-service
**Proyecto:** ActivitiesBoard
**Prioridad:** medium
**Estado:** done

## Qué se hizo
- `AttachmentUploadService extends FileUploadService` — primera implementación concreta del contrato abstracto de Common; solo define `getDirectory(): 'activities/attachments'`
- Binding DIP en `ActivitiesBoardServiceProvider`: `FileUploaderInterface::class → AttachmentUploadService::class` — los consumidores (ej: futuro `AdminController` en SF-04) dependen de la interfaz, nunca de la clase concreta
- Path resultante en tenant 3: `tenant_3/activities/attachments/2026/07/{hash}.pdf`

## Notas
- No hay consumidor todavía: SF-04 (AdminController multipart) integrará este servicio cuando exista el campo file en FormBuilder (SF-03).
- Si otro proyecto necesita subir archivos, replica este patrón: clase concreta + binding en su ServiceProvider.

## Archivos clave
- `app/Projects/ActivitiesBoard/Services/FileUpload/AttachmentUploadService.php`
- `app/Projects/ActivitiesBoard/Providers/ActivitiesBoardServiceProvider.php`
