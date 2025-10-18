<?php

namespace App\Projects\Landlord\Http\Controller\Admin;

use App\Attributes\RoutePrefix;
use App\Common\Admin\Controller\AdminController;
use App\Projects\Landlord\Adapters\Admin\TenantAdmin;

#[RoutePrefix('tenants')]
class TenantAdminController extends AdminController
{
    public function __construct()
    {
        $admin = new TenantAdmin();
        parent::__construct($admin);
    }
}
