<?php

namespace App\Common\Services\FileUpload\DTOs;

/**
 * Comando inmutable para eliminar un archivo almacenado.
 */
readonly class FileDeleteCommand
{
    public function __construct(
        public string $disk,
        public string $path,
    ) {
    }
}
