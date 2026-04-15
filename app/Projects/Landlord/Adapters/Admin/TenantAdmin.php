<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\Admin\Config\CreateViewConfig;
use App\Common\Admin\Config\EditViewConfig;
use App\Common\Admin\Config\ListViewConfig;
use App\Contracts\ProjectInterface;
use App\Models\Tenant;
use App\ProjectManager;
use App\Projects\Landlord\FormRequests\TenantFormRequest;
use App\Projects\Landlord\Http\Controller\Admin\TenantAdminController;
use App\Projects\Landlord\Services\Model\TenantService;

class TenantAdmin extends AdminBaseAdapter
{
    protected static string $controller = TenantAdminController::class;

    protected static string $model = Tenant::class;

    protected string $routePrefix = 'tenants';

    public function getFormRequest(): string
    {
        return TenantFormRequest::class;
    }

    public function getService(): string
    {
        return TenantService::class;
    }

    public function getTitle(): string
    {
        return __('landlord::messages.tenant.title');
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        $config->addStatCard(__('landlord::messages.tenant.stat_cards.total'), 0, [
            'icon'           => 'bi-building',
            'color'          => 'primary',
            'value_resolver' => fn ($items) => $items->total(),
        ]);

        $config->addStatCard(__('landlord::messages.tenant.stat_cards.active'), 0, [
            'icon'           => 'bi-check-circle',
            'color'          => 'success',
            'value_resolver' => fn ($items) => $items->filter(fn ($item) => ($item->data['status'] ?? null) === 'active')->count(),
        ]);

        $config->addStatCard(__('landlord::messages.tenant.stat_cards.pending'), 0, [
            'icon'           => 'bi-clock',
            'color'          => 'warning',
            'value_resolver' => fn ($items) => $items->filter(fn ($item) => ($item->data['status'] ?? null) === 'pending')->count(),
        ]);

        $config->addStatCard(__('landlord::messages.tenant.stat_cards.inactive'), 0, [
            'icon'           => 'bi-x-circle',
            'color'          => 'danger',
            'value_resolver' => fn ($items) => $items->filter(fn ($item) => ($item->data['status'] ?? null) === 'inactive')->count(),
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
            'domains.0.subdomain' => [
                'label'      => __('admin.columns.subdomain'),
                'sortable'   => false,
                'searchable' => false,
            ],
            'data.email' => [
                'label'      => __('admin.columns.email'),
                'sortable'   => false,
                'searchable' => false,
            ],
            'current_project' => [
                'label'      => __('admin.columns.project'),
                'sortable'   => true,
                'searchable' => false,
                'formatter'  => function ($value) {
                    if (! $value) {
                        return '<span class="badge bg-secondary">' . __('common.no_results') . '</span>';
                    }

                    $projects = ProjectManager::getProjects();
                    /** @var class-string<ProjectInterface> $projectClass */
                    foreach ($projects as $projectClass) {
                        if ($projectClass::getPrefix() === $value) {
                            return '<span class="badge bg-primary">' . $projectClass::getTitle() . '</span>';
                        }
                    }

                    return '<span class="badge bg-warning">' . $value . '</span>';
                },
            ],
            'data.status' => [
                'label'  => __('admin.columns.status'),
                'format' => 'badge',
                'class'  => 'text-center',
            ],
            'created_at' => [
                'label'    => __('admin.columns.created_at'),
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
        $config->emptyMessage(__('landlord::messages.tenant.empty'));

        return $config;
    }

    public function getCreateViewConfig(): CreateViewConfig
    {
        $config = parent::getCreateViewConfig();

        $config
            ->title(__('landlord::messages.tenant.create_title'))
            ->submitLabel(__('landlord::messages.tenant.create_submit'));

        return $config;
    }

    public function getEditViewConfig(mixed $item): EditViewConfig
    {
        $config = parent::getEditViewConfig($item);

        $itemData                    = $item->toArray();
        $itemData['email']           = $item->data['email'] ?? '';
        $itemData['status']          = $item->data['status'] ?? 'pending';
        $itemData['description']     = $item->data['description'] ?? '';
        $itemData['subdomain']       = $item->domains->first()->subdomain ?? '';
        $itemData['current_project'] = $item->current_project ?? '';
        $itemData['timezone']        = $item->timezone ?? 'UTC';
        $itemData['locale']          = $item->locale ?? 'es';

        $config
            ->title(__('landlord::messages.tenant.edit_title', ['name' => $item->name]))
            ->submitLabel(__('landlord::messages.tenant.edit_submit'))
            ->item((object) $itemData);

        return $config;
    }

    protected function getDeleteDisplayFields(): array
    {
        return [
            'id'         => __('landlord::messages.tenant.delete.id'),
            'name'       => __('landlord::messages.tenant.delete.name'),
            'identifier' => __('landlord::messages.tenant.delete.identifier'),
            'data.email' => __('landlord::messages.tenant.delete.email'),
            'data.status'=> __('landlord::messages.tenant.delete.status'),
            'created_at' => __('landlord::messages.tenant.delete.created_at'),
        ];
    }
}
