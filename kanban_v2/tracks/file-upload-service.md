# Track — Servicio de subida de archivos (file-upload-service)

## Estado actual
**Parcial (04/07/2026)** — backend implementado (SF-01, SF-02, SF-05): contratos, servicio abstracto, path resolver multi-tenant y primera implementación concreta en ActivitiesBoard, con 9 tests unitarios. Falta la capa UI/formulario (SF-03, SF-04, SF-06).

## Descripción

Sistema de subida de archivos basado en **Laravel Storage nativo (Flysystem)**. La capa `Common` expone un servicio abstracto que cada proyecto **debe** extender — la configuración de path es un contrato obligatorio, no opcional. El cambio local → S3 es de entorno, sin modificar código de aplicación.

---

## Librería elegida — Laravel Storage nativo (Filesystem / Flysystem)

- `Storage` facade + abstracción `Disk`, drivers `local`, `s3`, `ftp`, `sftp`, `r2` incluidos en `laravel/framework`.
- API uniforme: `Storage::disk($disk)->putFileAs($path, $file, $name)`.
- URL públicas para local, URL temporales firmadas para S3 (`Storage::temporaryUrl()`).
- Visibilidad por archivo (`public` / `private`).
- **Cero dependencias nuevas.**

### Cambio local → S3 (sin tocar código)

```dotenv
# .env desarrollo
UPLOAD_DISK=local

# .env producción
UPLOAD_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=mi-bucket
```

El servicio lee `config('filesystems.upload_disk', 'local')`. Ningún archivo PHP cambia.

---

## Arquitectura

### Principios aplicados
- **SRP**: `FileUploadService` coordina storage. `TenantPathResolver` resuelve rutas. Cada clase tiene una sola razón para cambiar.
- **OCP**: pipelines opcionales (`UploadPipelineInterface`) extienden comportamiento sin modificar el servicio base.
- **LSP**: cualquier subclase concreta de `FileUploadService` es intercambiable donde se espera `FileUploaderInterface`.
- **ISP**: `FileUploaderInterface` es pequeña: `upload`, `delete`, `url`. Sin métodos que las implementaciones no usen.
- **DIP**: `AdminController` depende de `FileUploaderInterface`, nunca de la clase concreta.

### Por qué el servicio Common es abstracto

`FileUploadService` en Common declara `getDirectory(): string` como **abstract**. Esto significa:

- No se puede instanciar `FileUploadService` directamente — PHP lo impide en tiempo de compilación.
- Cada proyecto que quiera subir archivos **debe** crear una clase concreta que defina su propio directorio.
- La lógica de upload, delete y resolución de path solo existe en un lugar (Common); el proyecto solo aporta su configuración.

```php
// Common — no instanciable
abstract class FileUploadService implements FileUploaderInterface
{
    // Obligatorio: cada proyecto define su directorio de destino
    abstract protected function getDirectory(): string;

    // Opcional: override para usar un disco distinto al de configuración
    protected function getDisk(): string
    {
        return config('filesystems.upload_disk', 'local');
    }

    // Implementados una sola vez en Common
    public function upload(UploadedFile $file): FileUploadResult { ... }
    public function delete(FileDeleteCommand $command): bool { ... }
    public function url(string $path): string { ... }
}

// Proyecto — concreta, PHP exige que defina getDirectory()
class AttachmentUploadService extends FileUploadService
{
    protected function getDirectory(): string
    {
        return 'activities/attachments';
    }
}
```

Si el desarrollador intenta registrar `FileUploadService` sin extenderla, PHP lanza `Cannot instantiate abstract class`.

### Estructura de archivos (Common layer)

```
app/Common/Services/FileUpload/
├── Contracts/
│   ├── FileUploaderInterface.php       ← upload() / delete() / url()
│   └── UploadPipelineInterface.php     ← process(UploadedFile): UploadedFile
├── DTOs/
│   ├── FileUploadResult.php            ← readonly: disk, path, url, mimeType, size
│   └── FileDeleteCommand.php           ← readonly: disk, path
├── FileUploadService.php               ← abstract, implements FileUploaderInterface
└── TenantPathResolver.php              ← prefija: tenant_{id}/{directory}/{filename}
```

### Estructura por proyecto (ejemplo)

```
app/Projects/ActivitiesBoard/
└── Services/FileUpload/
    └── AttachmentUploadService.php     ← extends FileUploadService, define getDirectory()
```

### Flujo de upload

```
Request (multipart/form-data)
    → FormRequest::validated()          [incluye UploadedFile]
    → AdminController::create/edit
    → FileUploaderInterface::upload(UploadedFile)
        → TenantPathResolver::resolve(getDisk(), getDirectory())
              → "tenant_42/activities/attachments/2026/07/abc123.pdf"
        → Storage::disk($disk)->putFileAs($path, $file, $filename)
    → FileUploadResult {disk, path, url, mimeType, size}
    → Service de dominio persiste path/url en modelo
```

### Aislamiento multi-tenant

`TenantPathResolver` prefija cada ruta con el ID del tenant activo. No hay forma de que un archivo de un tenant quede en el espacio de otro, independientemente del driver:

```
tenant_{id}/{directory}/{año}/{mes}/{filename_unico}
    ej: tenant_3/activities/attachments/2026/07/f4a1b2c3.pdf
```

### FormBuilder — campo file

Se agrega `FormBuilder::file(string $name, string $label, array $options = []): self`.
Cuando hay al menos un campo file, el formulario emite `enctype="multipart/form-data"` automáticamente.

---

## Sub-features

| # | Feature | Estado | Notas |
|---|---------|--------|-------|
| #01 | `FileUploaderInterface` + `FileUploadResult` DTO + `FileDeleteCommand` DTO | ✅ done | `done/file-upload-service/01_04072026` |
| #02 | `FileUploadService` abstract + `TenantPathResolver` | ✅ done | `done/file-upload-service/02_04072026` — incluye validación MIME server-side |
| #03 | `FormBuilder::file()` + Blade partial `_file-input.blade.php` | ⏳ pending | Integración UI |
| #04 | `AdminController` — manejo multipart en create/edit | ⏳ pending | Depende de #03 |
| #05 | Implementación concreta en un proyecto (ActivitiesBoard) | ✅ done | `done/file-upload-service/05_04072026` — binding DIP en provider |
| #06 | Admin UI — preview de imagen/archivo en edit + acción delete | ⏳ pending | Blade components: `file-preview`, `file-delete` |

## Deuda técnica anticipada
- ~~Validación MIME server-side (no solo extensión)~~ — ✅ resuelta en SF-02 vía `getAllowedMimeTypes()`.
- Quota por tenant — cuánto espacio puede usar cada tenant.
- Limpieza de archivos huérfanos si el modelo no se persiste (rollback de transacción DB).
- URL temporales para discos privados en S3 — SF-06 debe contemplarlo.
