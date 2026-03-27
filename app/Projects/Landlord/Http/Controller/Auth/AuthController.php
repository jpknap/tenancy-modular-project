<?php

namespace App\Projects\Landlord\Http\Controller\Auth;

use App\Common\Http\Controller\Auth\BaseAuthController;
use App\Projects\Landlord\Enums\Routes;

class AuthController extends BaseAuthController
{
    protected function guard(): string
    {
        return 'landlord';
    }

    protected function loginView(): string
    {
        return 'landlord.auth.login';
    }

    protected function defaultRedirectRoute(): string
    {
        return Routes::TenantList->value;
    }

    protected function loginRoute(): string
    {
        return Routes::Login->value;
    }
}
