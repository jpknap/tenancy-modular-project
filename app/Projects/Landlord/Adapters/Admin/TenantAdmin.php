<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
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

    public function getListableAttributes(): array
    {
        return ['id', 'created_at', 'updated_at'];
    }
}
