<?php

namespace App\Http\Controllers\Admin;

use App\Adapters\Admin\TenantAdmin;

class TenantAdminController extends AdminController
{
    public function __construct()
    {
        $admin = new TenantAdmin();
        parent::__construct($admin);
    }
}
