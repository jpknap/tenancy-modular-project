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

    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Obtiene el repositorio asociado al modelo
     * Debe ser implementado por cada adapter
     */
    abstract public function repository(): string;

    /**
     * Obtiene todos los registros usando el repositorio
     */
    public function getAll()
    {
        return app($this->repository())->all();
    }

    /**
     * Obtiene registros paginados usando el repositorio
     */
    public function paginate(int $perPage = 15)
    {
        return app($this->repository())->paginate($perPage);
    }

    /**
     * Encuentra un registro por ID usando el repositorio
     */
    public function find($id)
    {
        return app($this->repository())->find($id);
    }

    public function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }
}
