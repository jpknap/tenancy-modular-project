<?php

namespace App\Common\Admin\Contracts;

use App\Common\Repository\Contracts\RepositoryInterface;

interface AdminAdapterInterface
{
    public function getUrl(string $action, array $params = []): string;

    public static function getController(): string;

    public function getRepository(): RepositoryInterface;

    public function getFormRequest(): string;

    public function getService(): string;
}
