<?php

namespace App\Http\Controllers\Admin;

use App\Adapters\Admin\UserAdmin;

class UserAdminController extends AdminController
{
    public function __construct()
    {
        $admin = new UserAdmin();
        parent::__construct($admin);
    }
}
