<?php

namespace App\Common\Services\FileUpload\Exceptions;

class InvalidMimeTypeException extends FileUploadException
{
    public static function forMimeType(string $mimeType, array $allowed): self
    {
        return new self(sprintf(
            'MIME type "%s" no permitido. Permitidos: %s',
            $mimeType,
            implode(', ', $allowed),
        ));
    }
}
