<?php

namespace App\Projects\ActivitiesBoard\Enums;

enum Routes: string
{
    // Auth
    case Login     = 'activities-board.auth.login';
    case LoginPost = 'activities-board.auth.login.post';
    case Logout    = 'activities-board.auth.logout';

    // Activities
    case ActivityList   = 'activities-board.admin.activities.list';
    case ActivityCreate = 'activities-board.admin.activities.create';
    case ActivityEdit   = 'activities-board.admin.activities.edit';
    case ActivityDelete = 'activities-board.admin.activities.delete';

    // Users
    case UserList   = 'activities-board.admin.users.list';
    case UserCreate = 'activities-board.admin.users.create';
    case UserEdit   = 'activities-board.admin.users.edit';
    case UserDelete = 'activities-board.admin.users.delete';

    public function route(mixed ...$parameters): string
    {
        return route($this->value, $parameters);
    }
}
