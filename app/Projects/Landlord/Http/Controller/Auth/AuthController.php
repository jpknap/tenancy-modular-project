<?php

namespace App\Projects\Landlord\Http\Controller\Auth;

use App\Common\Http\Controller\Auth\BaseAuthController;

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

    protected function defaultRedirect(): string
    {
        return '/landlord/admin/tenant/list';
    }
}
