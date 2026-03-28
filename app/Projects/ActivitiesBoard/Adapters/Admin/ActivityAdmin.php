<?php

namespace App\Projects\ActivitiesBoard\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\Admin\Config\CreateViewConfig;
use App\Common\Admin\Config\EditViewConfig;
use App\Common\Admin\Config\ListViewConfig;
use App\Projects\ActivitiesBoard\Http\Controller\Admin\ActivityAdminController;
use App\Projects\ActivitiesBoard\FormRequests\ActivityFormRequest;
use App\Projects\ActivitiesBoard\Models\Activity;

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
        return \App\Projects\ActivitiesBoard\Services\Model\ActivityService::class;
    }

    public function getTitle(): string
    {
        return 'Actividades';
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        $config->addStatCard('Total Actividades', 0, [
            'icon' => 'bi-list-check',
            'color' => 'primary',
            'value_resolver' => fn ($items) => $items->total(),
        ]);

        $config->addStatCard('Creadas Hoy', 0, [
            'icon' => 'bi-calendar-check',
            'color' => 'success',
            'value_resolver' => fn ($items) => $items->filter(function($item) {
                return $item->created_at->isToday();
            })->count(),
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
            'description' => [
                'label' => 'Descripción',
                'sortable' => false,
                'truncate' => 50,
            ],
            'created_at' => [
                'label' => 'Fecha Creación',
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
        $config->emptyMessage('No hay actividades registradas');

        return $config;
    }

    public function getCreateViewConfig(): CreateViewConfig
    {
        $config = parent::getCreateViewConfig();
        $config->title('Crear Nueva Actividad')->submitLabel('Crear Actividad');
        return $config;
    }

    public function getEditViewConfig(mixed $item): EditViewConfig
    {
        $config = parent::getEditViewConfig($item);
        $config->title('Editar Actividad: ' . $item->name)->submitLabel('Actualizar Actividad');
        return $config;
    }

    protected function getDeleteDisplayFields(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'description' => 'Descripción',
        ];
    }
}
