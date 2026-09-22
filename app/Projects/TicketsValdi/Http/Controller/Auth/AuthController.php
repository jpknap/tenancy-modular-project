<?php

namespace App\Projects\TicketsValdi\Http\Controller\Auth;

use App\Common\Http\Controller\Auth\BaseAuthController;
use App\Projects\TicketsValdi\Enums\Routes;

class AuthController extends BaseAuthController
{
    protected function guard(): string
    {
        return 'web';
    }

    protected function loginView(): string
    {
        return 'tickets-valdi.auth.login';
    }

    protected function defaultRedirectRoute(): string
    {
        return Routes::UserList->value;
    }

    protected function loginRoute(): string
    {
        return Routes::Login->value;
    }
}
