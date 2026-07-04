<?php

namespace App\Common\Services\FileUpload\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * Transformación opcional aplicada al archivo antes de almacenarlo
 * (ej: resize de imagen, compresión, sanitización de nombre).
 */
interface UploadPipelineInterface
{
    public function process(UploadedFile $file): UploadedFile;
}
