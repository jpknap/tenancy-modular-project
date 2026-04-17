<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\Admin\Config\EditViewConfig;
use App\Common\Admin\Config\ListViewConfig;
use App\Models\User;
use App\Projects\Landlord\FormRequests\UserFormRequest;
use App\Projects\Landlord\Http\Controller\Admin\UserAdminController;
use App\Projects\Landlord\Services\Model\UserService;

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
        return __('admin.stat_cards.total_users');
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        $config->addStatCard(__('admin.stat_cards.total_users'), 0, [
            'icon' => 'bi-people',
            'color' => 'primary',
            'value_resolver' => fn ($items) => 9,
        ]);

        $config->addStatCard(__('admin.stat_cards.active'), 0, [
            'icon' => 'bi-check-circle',
            'color' => 'success',
            'value_resolver' => fn ($items) => $items->where('is_active', true)
                ->count(),
        ]);

        $config->addStatCard(__('admin.stat_cards.admins'), 0, [
            'icon' => 'bi-shield-check',
            'color' => 'info',
            'value_resolver' => fn ($items) => $items->where('role', 'admin')
                ->count(),
        ]);

        $config->addStatCard(__('admin.stat_cards.inactive'), 0, [
            'icon' => 'bi-x-circle',
            'color' => 'danger',
            'value_resolver' => fn ($items) => $items->where('is_active', false)
                ->count(),
        ]);

        $config->columns([
            'id' => [
                'label' => __('admin.columns.id'),
                'sortable' => true,
                'class' => 'text-center',
            ],
            'name' => [
                'label' => __('admin.columns.name'),
                'sortable' => true,
                'searchable' => true,
            ],
            'email' => [
                'label' => __('admin.columns.email'),
                'sortable' => true,
                'searchable' => true,
            ],
            'enabled' => [
                'label' => __('admin.columns.active'),
                'format' => 'boolean',
                'class' => 'text-center',
            ],
            'created_at' => [
                'label' => __('admin.columns.registered_at'),
                'format' => 'datetime',
                'sortable' => true,
            ],
        ]);

        $config->addAction(__('admin.actions.edit'), $this->getUrlName('edit'), [
            'icon' => 'bi-pencil text-primary',
            'route_params' => [
                'id' => 'id',
            ],
        ]);

        $config->addAction('Suplantar', $this->getUrlName('impersonate'), [
            'icon' => 'bi-person-fill-gear text-warning',
            'type' => 'form',
            'form_method' => 'POST',
            'route_params' => [
                'id' => 'id',
            ],
            'permission' => 'users:impersonate',
            'confirm' => true,
            'confirm_message' => '¿Deseas iniciar sesión como este usuario?',
        ]);

        $config->addAction(__('admin.actions.delete'), $this->getUrlName('delete'), [
            'icon' => 'bi-trash text-danger',
            'route_params' => [
                'id' => 'id',
            ],
        ]);

        $config->perPage(20);
        $config->emptyMessage(__('admin.empty'));

        return $config;
    }

    public function getEditViewConfig(mixed $item): EditViewConfig
    {
        if (! $item->timezone) {
            $item->timezone = resolve_tenant_timezone();
        }

        return parent::getEditViewConfig($item);
    }

    protected function getDeleteDisplayFields(): array
    {
        return [
            'id' => __('admin.columns.id'),
            'name' => __('admin.columns.name'),
            'email' => __('admin.columns.email'),
            'created_at' => __('admin.columns.registered_at'),
        ];
    }
}
