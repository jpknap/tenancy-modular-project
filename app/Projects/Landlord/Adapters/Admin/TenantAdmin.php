<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Models\Tenant;
use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Projects\Landlord\Http\Controller\Admin\TenantAdminController;

class TenantAdmin extends AdminBaseAdapter
{
    protected string $model = Tenant::class;

    protected string $routePrefix = 'tenant';

    public static string $controller = TenantAdminController::class;

    public function getListableAttributes(): array
    {
        return ['id'];
    }
}
