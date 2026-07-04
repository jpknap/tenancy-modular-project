# 01 — Contratos base + DTOs (FileUploaderInterface, FileUploadResult, FileDeleteCommand)

**Track:** file-upload-service
**Proyecto:** core (Common)
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `FileUploaderInterface` — contrato mínimo (ISP): `upload(UploadedFile): FileUploadResult`, `delete(FileDeleteCommand): bool`, `url(string): string`
- `UploadPipelineInterface` — `process(UploadedFile): UploadedFile`, punto de extensión OCP para transformaciones previas al almacenamiento (resize, compresión, etc.)
- `FileUploadResult` — DTO `readonly`: `disk`, `path`, `url`, `mimeType`, `size` + `toArray()` para persistencia
- `FileDeleteCommand` — DTO `readonly`: `disk`, `path`
- Excepciones propias: `FileUploadException` (base) e `InvalidMimeTypeException` (con factory `forMimeType()`)

## Archivos clave
- `app/Common/Services/FileUpload/Contracts/FileUploaderInterface.php`
- `app/Common/Services/FileUpload/Contracts/UploadPipelineInterface.php`
- `app/Common/Services/FileUpload/DTOs/FileUploadResult.php`
- `app/Common/Services/FileUpload/DTOs/FileDeleteCommand.php`
- `app/Common/Services/FileUpload/Exceptions/FileUploadException.php`
- `app/Common/Services/FileUpload/Exceptions/InvalidMimeTypeException.php`
