<?php

namespace App\Projects\TicketsValdi\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\Admin\Config\CreateViewConfig;
use App\Common\Admin\Config\EditViewConfig;
use App\Common\Admin\Config\ListViewConfig;
use App\Projects\TicketsValdi\FormRequests\InstitutionFormRequest;
use App\Projects\TicketsValdi\Http\Controller\Admin\InstitutionAdminController;
use App\Projects\TicketsValdi\Models\Institution;
use App\Projects\TicketsValdi\Services\Model\InstitutionService;

/**
 * No expone acción "delete": una institución con eventos/órdenes vinculados no
 * debería poder borrarse desde el listado. Se deshabilita (`enabled = false`) en
 * lugar de eliminarse. Ver docs/DOMAIN.md.
 */
class InstitutionAdmin extends AdminBaseAdapter
{
    protected static string $controller = InstitutionAdminController::class;

    protected static string $model = Institution::class;

    protected string $routePrefix = 'institutions';

    public function getFormRequest(): string
    {
        return InstitutionFormRequest::class;
    }

    public function getService(): string
    {
        return InstitutionService::class;
    }

    public function getTitle(): string
    {
        return __('tickets-valdi::messages.institution.title');
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        $config->addStatCard(__('tickets-valdi::messages.institution.stat_cards.total'), 0, [
            'icon' => 'bi-building',
            'color' => 'primary',
            'value_resolver' => fn ($items) => $items->total(),
        ]);

        $config->addStatCard(__('tickets-valdi::messages.institution.stat_cards.enabled'), 0, [
            'icon' => 'bi-check-circle',
            'color' => 'success',
            'value_resolver' => fn ($items) => $items->where('enabled', true)
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
            'description' => [
                'label' => __('admin.columns.description'),
                'sortable' => false,
                'truncate' => 50,
            ],
            'enabled' => [
                'label' => __('admin.columns.active'),
                'format' => 'boolean',
                'class' => 'text-center',
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

        $config->perPage(20);
        $config->emptyMessage(__('tickets-valdi::messages.institution.empty'));

        return $config;
    }

    public function getCreateViewConfig(): CreateViewConfig
    {
        $config = parent::getCreateViewConfig();
        $config
            ->title(__('tickets-valdi::messages.institution.create_title'))
            ->submitLabel(__('tickets-valdi::messages.institution.create_submit'));

        return $config;
    }

    public function getEditViewConfig(mixed $item): EditViewConfig
    {
        $config = parent::getEditViewConfig($item);
        $config
            ->title(__('tickets-valdi::messages.institution.edit_title', [
                'name' => $item->name,
            ]))
            ->submitLabel(__('tickets-valdi::messages.institution.edit_submit'));

        return $config;
    }
}
