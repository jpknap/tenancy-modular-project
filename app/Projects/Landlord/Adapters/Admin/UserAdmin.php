<?php

namespace App\Projects\Landlord\Adapters\Admin;

use App\Common\Admin\Adapter\AdminBaseAdapter;
use App\Models\User;
use App\Projects\Landlord\Http\Controller\Admin\UserAdminController;
use App\Projects\Landlord\Repositories\UserRepository;
use App\Projects\Landlord\Requests\UserFormRequest;

class UserAdmin extends AdminBaseAdapter
{
    public static string $controller = UserAdminController::class;

    protected string $model = User::class;

    protected string $routePrefix = 'user';

    public function repository(): string
    {
        return UserRepository::class;
    }

    public function getFormRequest(): string
    {
        return UserFormRequest::class;
    }

    public function getTitle(): string
    {
        return 'Usuarios';
    }

    public function getListableAttributes(): array
    {
        return ['id', 'name', 'email', 'created_at'];
    }
}
