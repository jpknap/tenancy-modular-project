<?php

namespace App\Common\Services\FileUpload\Contracts;

use App\Common\Services\FileUpload\DTOs\FileDeleteCommand;
use App\Common\Services\FileUpload\DTOs\FileUploadResult;
use Illuminate\Http\UploadedFile;

interface FileUploaderInterface
{
    public function upload(UploadedFile $file): FileUploadResult;

    public function delete(FileDeleteCommand $command): bool;

    public function url(string $path): string;
}
