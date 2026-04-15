<?php

namespace App\Projects\ActivitiesBoard\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\Admin\Config\CreateViewConfig;
use App\Common\Admin\Config\EditViewConfig;
use App\Common\Admin\Config\ListViewConfig;
use App\Projects\ActivitiesBoard\FormRequests\UserFormRequest;
use App\Projects\ActivitiesBoard\Http\Controller\Admin\UserAdminController;
use App\Projects\ActivitiesBoard\Models\User;
use App\Projects\ActivitiesBoard\Services\Model\UserService;

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
        return __('activities-board::messages.user.title');
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        $config->addStatCard(__('activities-board::messages.user.stat_cards.total'), 0, [
            'icon'           => 'bi-people-fill',
            'color'          => 'primary',
            'value_resolver' => fn ($items) => $items->total(),
        ]);

        $config->addStatCard(__('activities-board::messages.user.stat_cards.active'), 0, [
            'icon'           => 'bi-person-check',
            'color'          => 'success',
            'value_resolver' => fn ($items) => $items->total(),
        ]);

        $config->columns([
            'id' => [
                'label'    => __('admin.columns.id'),
                'sortable' => true,
                'class'    => 'text-center',
            ],
            'name' => [
                'label'      => __('admin.columns.name'),
                'sortable'   => true,
                'searchable' => true,
            ],
            'email' => [
                'label'      => __('admin.columns.email'),
                'sortable'   => true,
                'searchable' => true,
            ],
            'role' => [
                'label' => 'Rol',
                'sortable' => false,
                'value_resolver' => fn ($user) => $user->getRoleNames()->first() ?? 'Sin rol',
            ],
            'created_at' => [
                'label'    => __('admin.columns.registered_at'),
                'format'   => 'datetime',
                'sortable' => true,
            ],
        ]);

        $config->addAction(__('admin.actions.edit'), $this->getUrlName('edit'), [
            'icon'         => 'bi-pencil text-primary',
            'route_params' => ['id' => 'id'],
        ]);

        $config->addAction(__('admin.actions.delete'), $this->getUrlName('delete'), [
            'icon'         => 'bi-trash text-danger',
            'route_params' => ['id' => 'id'],
        ]);

        $config->perPage(15);
        $config->emptyMessage(__('activities-board::messages.user.empty'));

        return $config;
    }

    public function getCreateViewConfig(): CreateViewConfig
    {
        $config = parent::getCreateViewConfig();
        $config
            ->title(__('activities-board::messages.user.create_title'))
            ->submitLabel(__('activities-board::messages.user.create_submit'));

        return $config;
    }

    public function getEditViewConfig(mixed $item): EditViewConfig
    {
        if (! $item->timezone) {
            $item->timezone = resolve_tenant_timezone();
        }

        $config = parent::getEditViewConfig($item);
        $config
            ->title(__('activities-board::messages.user.edit_title', ['name' => $item->name]))
            ->submitLabel(__('activities-board::messages.user.edit_submit'));

        return $config;
    }

    protected function getDeleteDisplayFields(): array
    {
        return [
            'id'    => __('admin.columns.id'),
            'name'  => __('admin.columns.name'),
            'email' => __('admin.columns.email'),
        ];
    }
}
