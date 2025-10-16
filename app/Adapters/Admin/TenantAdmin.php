<?php

namespace App\Adapters\Admin;

use App\Http\Controllers\Admin\TenantAdminController;
use App\Models\Tenant;
use App\Module\Admin\Adapter\AdminBaseAdapter;

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
