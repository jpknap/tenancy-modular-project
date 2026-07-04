<?php

namespace App\Common\Services\FileUpload;

use App\Common\Services\FileUpload\Contracts\FileUploaderInterface;
use App\Common\Services\FileUpload\Contracts\UploadPipelineInterface;
use App\Common\Services\FileUpload\DTOs\FileDeleteCommand;
use App\Common\Services\FileUpload\DTOs\FileUploadResult;
use App\Common\Services\FileUpload\Exceptions\FileUploadException;
use App\Common\Services\FileUpload\Exceptions\InvalidMimeTypeException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Servicio base de subida de archivos sobre Laravel Storage (Flysystem).
 *
 * Abstracto a propósito: cada proyecto debe extenderlo y definir
 * getDirectory(). La lógica de upload/delete/url vive solo acá.
 */
abstract class FileUploadService implements FileUploaderInterface
{
    public function __construct(
        protected TenantPathResolver $pathResolver,
    ) {
    }

    public function upload(UploadedFile $file): FileUploadResult
    {
        $this->validateMimeType($file);

        foreach ($this->getPipelines() as $pipeline) {
            $file = $pipeline->process($file);
        }

        $disk = $this->getDisk();
        $directory = $this->pathResolver->resolve($this->getDirectory());
        $path = Storage::disk($disk)->putFileAs($directory, $file, $file->hashName());

        if ($path === false) {
            throw new FileUploadException("No se pudo almacenar el archivo en el disco \"{$disk}\"");
        }

        return new FileUploadResult(
            disk: $disk,
            path: $path,
            url: $this->url($path),
            mimeType: (string) $file->getMimeType(),
            size: (int) $file->getSize(),
        );
    }

    public function delete(FileDeleteCommand $command): bool
    {
        return Storage::disk($command->disk)->delete($command->path);
    }

    public function url(string $path): string
    {
        return Storage::disk($this->getDisk())->url($path);
    }

    /**
     * Obligatorio: directorio de destino del proyecto (ej: 'activities/attachments')
     */
    abstract protected function getDirectory(): string;

    /**
     * Disco de destino. Cambia con UPLOAD_DISK sin tocar código.
     */
    protected function getDisk(): string
    {
        return config('filesystems.upload_disk', 'local');
    }

    /**
     * Pipelines opcionales aplicados al archivo antes de almacenarlo.
     *
     * @return UploadPipelineInterface[]
     */
    protected function getPipelines(): array
    {
        return [];
    }

    /**
     * MIME types permitidos (detección server-side por contenido).
     * null = sin restricción.
     */
    protected function getAllowedMimeTypes(): ?array
    {
        return null;
    }

    private function validateMimeType(UploadedFile $file): void
    {
        $allowed = $this->getAllowedMimeTypes();

        if ($allowed === null) {
            return;
        }

        // getMimeType() inspecciona el contenido real, no la extensión
        $mimeType = (string) $file->getMimeType();

        if (! in_array($mimeType, $allowed, true)) {
            throw InvalidMimeTypeException::forMimeType($mimeType, $allowed);
        }
    }
}
