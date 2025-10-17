<?php

namespace App\Projects\Landlord\Http\Controller\Admin;

use App\Projects\Landlord\Adapters\Admin\UserAdmin;

class UserAdminController extends AdminController
{
    public function __construct()
    {
        $admin = new UserAdmin();
        parent::__construct($admin);
    }
}
