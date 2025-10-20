<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Common\ListView\ListViewConfig;
use App\Models\Tenant;
use App\Projects\Landlord\Http\Controller\Admin\TenantAdminController;
use App\Projects\Landlord\Repositories\TenantRepository;
use App\Projects\Landlord\Requests\TenantFormRequest;
use App\Projects\Landlord\Services\Model\TenantService;

class TenantAdmin extends AdminBaseAdapter
{
    protected static string $controller = TenantAdminController::class;

    protected static string $model = Tenant::class;

    protected string $routePrefix = 'tenant';

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
        return 'Clientes';
    }

    public function getListViewConfig(): ListViewConfig
    {
        $config = new ListViewConfig();

        // Tarjetas de estadísticas
        $config->addStatCard('Total Registros', 0, [
            'icon' => 'bi-building',
            'color' => 'primary',
            'value_resolver' => fn($items) => $items->total(),
        ]);

        $config->addStatCard('Activos', 0, [
            'icon' => 'bi-check-circle',
            'color' => 'success',
            'value_resolver' => fn($items) => $items->where('status', 'active')->count(),
        ]);

        $config->addStatCard('Pendientes', 0, [
            'icon' => 'bi-clock',
            'color' => 'warning',
            'value_resolver' => fn($items) => $items->where('status', 'pending')->count(),
        ]);

        $config->addStatCard('Inactivos', 0, [
            'icon' => 'bi-x-circle',
            'color' => 'danger',
            'value_resolver' => fn($items) => $items->where('status', 'inactive')->count(),
        ]);

        // Columnas
        $config->columns([
            'id' => ['label' => 'ID', 'sortable' => true, 'class' => 'text-center'],
            'name' => ['label' => 'Nombre', 'sortable' => true, 'searchable' => true],
            'email' => ['label' => 'Email', 'sortable' => true, 'searchable' => true],
            'status' => ['label' => 'Estado', 'format' => 'badge', 'class' => 'text-center'],
            'created_at' => ['label' => 'Fecha Creación', 'format' => 'datetime', 'sortable' => true],
        ]);

        // Acciones por fila
        $config->addAction('Ver', 'landlord.admin.tenant.show', [
            'icon' => 'bi-eye text-info',
            'route_params' => ['id' => 'id'],
        ]);

        $config->addAction('Editar', 'landlord.admin.tenant.edit', [
            'icon' => 'bi-pencil text-primary',
            'route_params' => ['id' => 'id'],
        ]);

        $config->addAction('Eliminar', 'landlord.admin.tenant.destroy', [
            'icon' => 'bi-trash text-danger',
            'type' => 'form',
            'confirm' => true,
            'confirm_message' => '¿Está seguro de eliminar este tenant?',
            'route_params' => ['id' => 'id'],
        ]);

        // Paginación
        $config->perPage(15);

        // Mensaje vacío
        $config->emptyMessage('No hay tenants registrados');

        return $config;
    }
}
