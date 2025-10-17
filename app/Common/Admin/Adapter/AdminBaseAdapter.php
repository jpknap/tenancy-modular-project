<?php

namespace App\Module\Admin\Adapter;

abstract class AdminBaseAdapter
{
    protected string $model = '';

    protected string $routePrefix = '';

    protected string $controller = '';

    public function getController(): string
    {
        return $this->controller;
    }

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
