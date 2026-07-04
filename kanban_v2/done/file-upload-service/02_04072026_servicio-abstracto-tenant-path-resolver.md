# 02 — FileUploadService abstract + TenantPathResolver

**Track:** file-upload-service
**Proyecto:** core (Common)
**Prioridad:** high
**Estado:** done

## Qué se hizo
- `FileUploadService` **abstract** implementa `FileUploaderInterface` sobre Laravel Storage nativo (Flysystem, cero dependencias nuevas):
  - `getDirectory(): string` **abstract** — el contrato de path es obligatorio; PHP impide instanciar sin extender
  - `getDisk()` lee `config('filesystems.upload_disk', 'local')` — cambio local → S3 solo por entorno (`UPLOAD_DISK`)
  - `getPipelines(): array` — hook opcional, aplica cada `UploadPipelineInterface` antes de almacenar
  - `getAllowedMimeTypes(): ?array` — validación MIME **server-side por contenido** (`getMimeType()`, no extensión); `null` = sin restricción. Cubre la deuda técnica anticipada del track
  - `upload()` usa `putFileAs()` con `hashName()` (nombre único), lanza `FileUploadException` si el disco falla, devuelve `FileUploadResult`
- `TenantPathResolver` — aislamiento multi-tenant de rutas:
  - Formato: `tenant_{id}/{directory}/{año}/{mes}` (contexto tenant) o `landlord/{directory}/{año}/{mes}` (contexto landlord)
  - `getTenantId()` protected consulta `tenancy()->initialized` — overrideable en tests sin bootear tenancy
- Config: clave `upload_disk` en `config/filesystems.php` + `UPLOAD_DISK=local` en `.env.example`

## Tests (9 pasando)
- `tests/Unit/FileUpload/TenantPathResolverTest.php` — prefijo landlord, prefijo tenant, trim de slashes
- `tests/Unit/FileUpload/FileUploadServiceTest.php` — upload bajo path de tenant, nombres únicos, delete, rechazo/aceptación de MIME, ejecución de pipelines (con `Storage::fake`)

## Notas
- Los fakes de Laravel resuelven MIME por extensión (`MimeType::from($name)`); en producción `getMimeType()` inspecciona contenido real vía fileinfo.
- `url()` delega en `Storage::url()`; URLs temporales firmadas para S3 privado quedan para SF-06.

## Archivos clave
- `app/Common/Services/FileUpload/FileUploadService.php`
- `app/Common/Services/FileUpload/TenantPathResolver.php`
- `config/filesystems.php` (clave `upload_disk`)
