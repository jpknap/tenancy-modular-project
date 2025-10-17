<?php

namespace App\Projects\Landlord\Http\Controller\Admin;

use App\Projects\Landlord\Adapters\Admin\TenantAdmin;

class TenantAdminController extends AdminController
{
    public function __construct()
    {
        $admin = new TenantAdmin();
        parent::__construct($admin);
    }
}
