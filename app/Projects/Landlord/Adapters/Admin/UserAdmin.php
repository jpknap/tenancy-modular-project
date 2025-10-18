<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Models\User;
use App\Projects\Landlord\Http\Controller\Admin\UserAdminController;
use App\Projects\Landlord\Repositories\UserRepository;

class UserAdmin extends AdminBaseAdapter
{
    protected string $model = User::class;

    protected string $routePrefix = 'user';

    public static string $controller  = UserAdminController::class;

    public function repository(): string
    {
        return UserRepository::class;
    }

    public function getTitle(): string
    {
        return ' Usuarios';
    }
    
    public function getListableAttributes(): array
    {
        return ['id', 'name', 'email', 'created_at'];
    }
}
