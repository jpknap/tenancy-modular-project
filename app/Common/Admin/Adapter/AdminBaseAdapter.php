<?php

namespace App\Common\Admin\Adapter;

abstract class AdminBaseAdapter
{
    protected string $model = '';

    protected string $routePrefix = '';

    public static string $controller  = '';

    public function getListableAttributes(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return '';
    }

    public function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }
}
