<?php

namespace App\Projects\ActivitiesBoard\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\Admin\Config\CreateViewConfig;
use App\Common\Admin\Config\EditViewConfig;
use App\Common\Admin\Config\ListViewConfig;
use App\Projects\ActivitiesBoard\FormRequests\ActivityFormRequest;
use App\Projects\ActivitiesBoard\Http\Controller\Admin\ActivityAdminController;
use App\Projects\ActivitiesBoard\Models\Activity;
use App\Projects\ActivitiesBoard\Services\Model\ActivityService;

class ActivityAdmin extends AdminBaseAdapter
{
    protected static string $controller = ActivityAdminController::class;

    protected static string $model = Activity::class;

    protected string $routePrefix = 'activities';

    public function getFormRequest(): string
    {
        return ActivityFormRequest::class;
    }

    public function getService(): string
    {
        return ActivityService::class;
    }

    public function getTitle(): string
    {
        return __('activities-board::messages.activity.title');
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        $config->addStatCard(__('activities-board::messages.activity.stat_cards.total'), 0, [
            'icon' => 'bi-list-check',
            'color' => 'primary',
            'value_resolver' => fn ($items) => $items->total(),
        ]);

        $config->addStatCard(__('activities-board::messages.activity.stat_cards.today'), 0, [
            'icon' => 'bi-calendar-check',
            'color' => 'success',
            'value_resolver' => fn ($items) => $items->filter(fn ($item) => $item->created_at->isToday())->count(),
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
            'description' => [
                'label' => __('admin.columns.description'),
                'sortable' => false,
                'truncate' => 50,
            ],
            'created_at' => [
                'label' => __('admin.columns.created_at'),
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

        $config->perPage(15);
        $config->emptyMessage(__('activities-board::messages.activity.empty'));

        return $config;
    }

    public function getCreateViewConfig(): CreateViewConfig
    {
        $config = parent::getCreateViewConfig();
        $config
            ->title(__('activities-board::messages.activity.create_title'))
            ->submitLabel(__('activities-board::messages.activity.create_submit'));

        return $config;
    }

    public function getEditViewConfig(mixed $item): EditViewConfig
    {
        $config = parent::getEditViewConfig($item);
        $config
            ->title(__('activities-board::messages.activity.edit_title', [
                'name' => $item->name,
            ]))
            ->submitLabel(__('activities-board::messages.activity.edit_submit'));

        return $config;
    }

    protected function getDeleteDisplayFields(): array
    {
        return [
            'id' => __('admin.columns.id'),
            'name' => __('admin.columns.name'),
            'description' => __('admin.columns.description'),
        ];
    }
}
