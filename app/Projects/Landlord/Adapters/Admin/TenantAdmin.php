<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Models\Tenant;
use App\Module\Admin\Adapter\AdminBaseAdapter;
use App\Projects\Landlord\Http\Controller\Admin\TenantAdminController;

final class TenantAdmin extends AdminBaseAdapter
{
    protected string $model = Tenant::class;

    protected string $routePrefix = 'tenant';

    protected string $controller = TenantAdminController::class;

    public function getListableAttributes(): array
    {
        return ['id'];
    }
}
