<?php

namespace App\Common\Admin\Adapter;

use App\Common\Admin\Contracts\AdminAdapterInterface;
use App\Common\ListView\ListViewConfig;
use App\Common\Repository\RepositoryManager;
use App\Common\Repository\Contracts\RepositoryInterface;

abstract class AdminBaseAdapter implements AdminAdapterInterface
{
    protected static string $controller = '';
    protected static string $model = '';
    protected string $routePrefix = '';

    public function __construct(
        protected RepositoryManager $repositoryManager
    )
    {
    }

    public static function getController(): string
    {
        return static::$controller;
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        $config->columns([
            'id' => 'ID',
            'created_at' => ['label' => 'Creado', 'format' => 'datetime'],
        ]);

        return $config;
    }

    /**
     * @deprecated Usar getListViewConfig() en su lugar
     */
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
        return static::$model;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repositoryManager->get(static::$model);
    }

    abstract public function getFormRequest(): string;

    public function getService(): string
    {
        return '';
    }

    public function getAll()
    {
        return $this->getRepository()->all();
    }

    public function paginate(int $perPage = 15)
    {
        return $this->getRepository()->paginate($perPage);
    }

    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    public function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }
}
