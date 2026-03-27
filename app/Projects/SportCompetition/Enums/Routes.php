<?php

namespace App\Projects\SportCompetition\Enums;

enum Routes: string
{
    // Auth
    case Login     = 'sport-competition.auth.login';
    case LoginPost = 'sport-competition.auth.login.post';
    case Logout    = 'sport-competition.auth.logout';

    // Users
    case UserList   = 'sport-competition.admin.users.list';
    case UserCreate = 'sport-competition.admin.users.create';
    case UserEdit   = 'sport-competition.admin.users.edit';
    case UserDelete = 'sport-competition.admin.users.delete';

    public function route(mixed ...$parameters): string
    {
        return route($this->value, $parameters);
    }
}
