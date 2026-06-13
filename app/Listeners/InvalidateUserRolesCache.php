<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use Spatie\Permission\Events\RoleAttachedEvent;
use Spatie\Permission\Events\RoleDetachedEvent;

class InvalidateUserRolesCache
{
    public function handle(RoleAttachedEvent|RoleDetachedEvent $event): void
    {
        if (! tenancy()->initialized) {
            return;
        }

        if (! $event->model instanceof User) {
            return;
        }

        $tenantKey = tenancy()->tenant->getTenantKey();
        cache()->forget("user.{$event->model->id}.roles.tenant.{$tenantKey}");
    }
}
