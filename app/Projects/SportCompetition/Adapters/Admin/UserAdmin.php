<?php

namespace App\Projects\SportCompetition\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\Admin\Config\ListViewConfig;
use App\Models\User;
use App\Projects\SportCompetition\Http\Controller\Admin\UserAdminController;
use App\Projects\SportCompetition\FormRequests\UserFormRequest;
use App\Projects\SportCompetition\Services\Model\UserService;

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
        return UserService::class;
    }

    public function getTitle(): string
    {
        return 'Usuarios';
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        $config->addStatCard('Total Usuarios', 0, [
            'icon' => 'bi-people',
            'color' => 'primary',
            'value_resolver' => fn ($items) => $items->total(),
        ]);

        $config->addStatCard('Activos', 0, [
            'icon' => 'bi-check-circle',
            'color' => 'success',
            'value_resolver' => fn ($items) => $items->where('is_active', true)
                ->count(),
        ]);

        $config->addStatCard('Administradores', 0, [
            'icon' => 'bi-shield-check',
            'color' => 'info',
            'value_resolver' => fn ($items) => $items->where('role', 'admin')
                ->count(),
        ]);

        $config->addStatCard('Inactivos', 0, [
            'icon' => 'bi-x-circle',
            'color' => 'danger',
            'value_resolver' => fn ($items) => $items->where('is_active', false)
                ->count(),
        ]);

        // Columnas
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
                'label' => 'Email',
                'sortable' => true,
                'searchable' => true,
            ],
            'enabled' => [
                'label' => 'Activo',
                'format' => 'boolean',
                'class' => 'text-center',
            ],
            'created_at' => [
                'label' => 'Fecha Registro',
                'format' => 'datetime',
                'sortable' => true,
            ],
        ]);

        // Acciones por fila
        $config->addAction('Editar', $this->getUrlName('edit'), [
            'icon' => 'bi-pencil text-primary',
            'route_params' => [
                'id' => 'id',
            ],
        ]);

        $config->addAction('Suplantar', $this->getUrlName('impersonate'), [
            'icon' => 'bi-person-fill-gear text-warning',
            'type' => 'form',
            'form_method' => 'POST',
            'route_params' => ['id' => 'id'],
            'permission' => 'users:impersonate',
            'confirm' => true,
            'confirm_message' => '¿Deseas iniciar sesión como este usuario?',
        ]);

        $config->addAction('Eliminar', $this->getUrlName('delete'), [
            'icon' => 'bi-trash text-danger',
            'route_params' => [
                'id' => 'id',
            ],
        ]);

        // Paginación
        $config->perPage(20);

        // Mensaje vacío
        $config->emptyMessage('No hay usuarios registrados');

        return $config;
    }

    protected function getDeleteDisplayFields(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'email' => 'Email',
            'created_at' => 'Fecha de Registro',
        ];
    }
}
