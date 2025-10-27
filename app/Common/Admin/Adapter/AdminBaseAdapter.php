<?php

namespace App\Common\Admin\Adapter;

use App\Common\Admin\Config\CreateViewConfig;
use App\Common\Admin\Config\EditViewConfig;
use App\Common\Admin\Config\ListViewConfig;
use App\Common\Admin\Contracts\AdminAdapterInterface;
use App\Common\Admin\Enum\FormContextEnum;
use App\Common\Repository\Contracts\RepositoryInterface;
use App\Common\Repository\RepositoryManager;
use App\ProjectManager;

abstract class AdminBaseAdapter implements AdminAdapterInterface
{
    protected static string $controller = '';

    protected static string $model = '';

    protected string $routePrefix = '';

    public function __construct(
        protected RepositoryManager $repositoryManager,
    ) {
    }

    public function getUrl(string $action, array $params = []): string
    {
        $routeName = $this->getUrlName($action);
        return route($routeName, $params);
    }

    public function getUrlName(string $action): string
    {
        $projectPrefix = ProjectManager::getCurrentProject()->getPrefix();
        return "{$projectPrefix}.admin.{$this->routePrefix}.{$action}";
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
            'created_at' => [
                'label' => 'Creado',
                'format' => 'datetime',
            ],
        ]);

        return $config;
    }

    public function getCreateViewConfig(): CreateViewConfig
    {
        $formRequestClass = $this->getFormRequest();
        $formRequest = new $formRequestClass();

        $config = new CreateViewConfig($formRequest->getFormBuilder(FormContextEnum::CREATE));

        $config
            ->title('Crear ' . $this->getTitle())
            ->submitLabel('Guardar');

        return $config;
    }

    public function getEditViewConfig(mixed $item): EditViewConfig
    {
        $formRequestClass = $this->getFormRequest();
        $formRequest = new $formRequestClass();

        $config = new EditViewConfig($formRequest->getFormBuilder(FormContextEnum::EDIT));

        $config
            ->title('Editar ' . $this->getTitle())
            ->submitLabel('Actualizar')
            ->item($item);

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
        return $this->getRepository()
            ->all();
    }

    public function paginate(int $perPage = 15)
    {
        return $this->getRepository()
            ->paginate($perPage);
    }

    public function find($id)
    {
        return $this->getRepository()
            ->find($id);
    }

    public function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }
}
