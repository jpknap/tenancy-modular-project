<?php

namespace App\Common\Services\FileUpload\DTOs;

/**
 * Resultado inmutable de una subida de archivo.
 */
readonly class FileUploadResult
{
    public function __construct(
        public string $disk,
        public string $path,
        public string $url,
        public string $mimeType,
        public int $size,
    ) {
    }

    public function toArray(): array
    {
        return [
            'disk' => $this->disk,
            'path' => $this->path,
            'url' => $this->url,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
        ];
    }
}
