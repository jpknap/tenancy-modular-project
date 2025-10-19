<?php

namespace App\Common\Admin\Adapter;

abstract class AdminBaseAdapter
{
    public static string $controller = '';

    protected string $model = '';

    protected string $routePrefix = '';

    public function getListableAttributes(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return '';
    }

    public function getModel(): string
    {
        return $this->model;
    }
    abstract public function repository(): string;
    public function getAll()
    {
        return app($this->repository())
            ->all();
    }

    public function paginate(int $perPage = 15)
    {
        return app($this->repository())
            ->paginate($perPage);
    }

    /**
     * Encuentra un registro por ID usando el repositorio
     */
    public function find($id)
    {
        return app($this->repository())
            ->find($id);
    }

    public function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }
}
