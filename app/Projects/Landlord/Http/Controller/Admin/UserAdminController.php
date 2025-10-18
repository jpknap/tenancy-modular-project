<?php

namespace App\Projects\Landlord\Http\Controller\Admin;

use App\Attributes\RoutePrefix;
use App\Common\Admin\Controller\AdminController;
use App\Projects\Landlord\Adapters\Admin\UserAdmin;
#[RoutePrefix('users')]
class UserAdminController extends AdminController
{
    public function __construct()
    {
        $admin = new UserAdmin();
        parent::__construct($admin);
    }
}
