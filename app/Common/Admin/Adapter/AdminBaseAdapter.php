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
     * Obtiene todos los registros del modelo
     */
    public function getAll()
    {
        $modelClass = $this->model;
        return $modelClass::all();
    }

    /**
     * Obtiene registros paginados del modelo
     */
    public function paginate(int $perPage = 15)
    {
        $modelClass = $this->model;
        return $modelClass::paginate($perPage);
    }

    /**
     * Encuentra un registro por ID
     */
    public function find($id)
    {
        $modelClass = $this->model;
        return $modelClass::find($id);
    }

    public function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }
}
