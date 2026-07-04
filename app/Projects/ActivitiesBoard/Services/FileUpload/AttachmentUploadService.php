<?php

namespace App\Projects\ActivitiesBoard\Services\FileUpload;

use App\Common\Services\FileUpload\FileUploadService;

/**
 * Uploader de adjuntos de actividades. Primera implementación concreta
 * del contrato abstracto de Common.
 */
class AttachmentUploadService extends FileUploadService
{
    protected function getDirectory(): string
    {
        return 'activities/attachments';
    }
}
