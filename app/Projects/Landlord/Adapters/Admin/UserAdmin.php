<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Models\User;
use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Projects\Landlord\Http\Controller\Admin\UserAdminController;

class UserAdmin extends AdminBaseAdapter
{
    protected string $model = User::class;

    protected string $routePrefix = 'user';

    public static string $controller  = UserAdminController::class;

    public function getTitle(): string
    {
        return 'Usuarios';
    }
    
    public function getListableAttributes(): array
    {
        return ['id', 'name', 'email', 'created_at'];
    }
}
