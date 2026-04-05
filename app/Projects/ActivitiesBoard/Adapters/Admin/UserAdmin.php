<?php

namespace App\Projects\ActivitiesBoard\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\Admin\Config\CreateViewConfig;
use App\Common\Admin\Config\EditViewConfig;
use App\Common\Admin\Config\ListViewConfig;
use App\Projects\ActivitiesBoard\Http\Controller\Admin\UserAdminController;
use App\Projects\ActivitiesBoard\FormRequests\UserFormRequest;
use App\Projects\ActivitiesBoard\Models\User;

class UserAdmin extends AdminBaseAdapter
{
    protected static string $controller = UserAdminController::class;
    protected static string $model = User::class;
    protected string $routePrefix = 'users';

    public function getFormRequest(): string
    {
        return UserFormRequest::class;
    }

    public function getService(): string
    {
        return \App\Projects\ActivitiesBoard\Services\Model\UserService::class;
    }

    public function getTitle(): string
    {
        return 'Usuarios';
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        $config->addStatCard('Total Usuarios', 0, [
            'icon' => 'bi-people-fill',
            'color' => 'primary',
            'value_resolver' => fn ($items) => $items->total(),
        ]);

        $config->addStatCard('Usuarios Activos', 0, [
            'icon' => 'bi-person-check',
            'color' => 'success',
            'value_resolver' => fn ($items) => $items->total(),
        ]);

        $config->columns([
            'id' => [
                'label' => 'ID',
                'sortable' => true,
                'class' => 'text-center',
            ],
            'name' => [
                'label' => 'Nombre',
                'sortable' => true,
                'searchable' => true,
            ],
            'email' => [
                'label' => 'Correo Electrónico',
                'sortable' => true,
                'searchable' => true,
            ],
            'created_at' => [
                'label' => 'Fecha Registro',
                'format' => 'datetime',
                'sortable' => true,
            ],
        ]);

        $config->addAction('Editar', $this->getUrlName('edit'), [
            'icon' => 'bi-pencil text-primary',
            'route_params' => ['id' => 'id'],
        ]);

        $config->addAction('Eliminar', $this->getUrlName('delete'), [
            'icon' => 'bi-trash text-danger',
            'route_params' => ['id' => 'id'],
        ]);

        $config->perPage(15);
        $config->emptyMessage('No hay usuarios registrados');

        return $config;
    }

    public function getCreateViewConfig(): CreateViewConfig
    {
        $config = parent::getCreateViewConfig();
        $config->title('Crear Nuevo Usuario')->submitLabel('Crear Usuario');
        return $config;
    }

    public function getEditViewConfig(mixed $item): EditViewConfig
    {
        if (! $item->timezone) {
            $item->timezone = resolve_tenant_timezone();
        }

        $config = parent::getEditViewConfig($item);
        $config->title('Editar Usuario: ' . $item->name)->submitLabel('Actualizar Usuario');
        return $config;
    }

    protected function getDeleteDisplayFields(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'email' => 'Correo Electrónico',
        ];
    }
}
