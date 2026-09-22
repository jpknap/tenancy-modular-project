<?php

namespace App\Projects\TicketsValdi\Enums;

enum Routes: string
{
    // Auth
    case Login = 'tickets-valdi.auth.login';
    case LoginPost = 'tickets-valdi.auth.login.post';
    case Logout = 'tickets-valdi.auth.logout';

    // Users
    case UserList = 'tickets-valdi.admin.users.list';
    case UserCreate = 'tickets-valdi.admin.users.create';
    case UserEdit = 'tickets-valdi.admin.users.edit';
    case UserDelete = 'tickets-valdi.admin.users.delete';

    public function route(mixed ...$parameters): string
    {
        return route($this->value, $parameters);
    }
}
