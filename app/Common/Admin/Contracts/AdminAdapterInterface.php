<?php

namespace App\Common\Admin\Contracts;

use App\Common\Repository\Contracts\RepositoryInterface;
use Illuminate\Routing\Controller;

interface AdminAdapterInterface
{
    public function getUrl(string $action): string;
    public static function getController(): string;
    public function getRepository(): RepositoryInterface;

    public function getFormRequest(): string;

    public function getService(): string;
}
