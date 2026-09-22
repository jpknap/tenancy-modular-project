<?php

namespace App\Projects\TicketsValdi\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\Admin\Config\ListViewConfig;
use App\Projects\TicketsValdi\FormRequests\UserFormRequest;
use App\Projects\TicketsValdi\Http\Controller\Admin\UserAdminController;
use App\Projects\TicketsValdi\Models\User;
use App\Projects\TicketsValdi\Services\Model\UserService;

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
        return __('tickets-valdi::messages.user.title');
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        $config->addStatCard(__('tickets-valdi::messages.user.stat_cards.total'), 0, [
            'icon' => 'bi-people',
            'color' => 'primary',
            'value_resolver' => fn ($items) => $items->total(),
        ]);

        $config->addStatCard(__('tickets-valdi::messages.user.stat_cards.active'), 0, [
            'icon' => 'bi-check-circle',
            'color' => 'success',
            'value_resolver' => fn ($items) => $items->where('enabled', true)
                ->count(),
        ]);

        $config->addStatCard(__('tickets-valdi::messages.user.stat_cards.inactive'), 0, [
            'icon' => 'bi-x-circle',
            'color' => 'danger',
            'value_resolver' => fn ($items) => $items->where('enabled', false)
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

        $config->addAction(__('admin.actions.delete'), $this->getUrlName('delete'), [
            'icon' => 'bi-trash text-danger',
            'route_params' => [
                'id' => 'id',
            ],
        ]);

        $config->perPage(20);
        $config->emptyMessage(__('tickets-valdi::messages.user.empty'));

        return $config;
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
