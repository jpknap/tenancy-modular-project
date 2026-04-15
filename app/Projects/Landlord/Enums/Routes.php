<?php

namespace App\Projects\Landlord\Enums;

enum Routes: string
{
    // Profile
    case ProfileEdit = 'landlord.profile.edit';

    // Auth
    case Login     = 'landlord.auth.login';
    case LoginPost = 'landlord.auth.login.post';
    case Logout    = 'landlord.auth.logout';

    // Tenants
    case TenantList   = 'landlord.admin.tenants.list';
    case TenantCreate = 'landlord.admin.tenants.create';
    case TenantEdit   = 'landlord.admin.tenants.edit';
    case TenantDelete = 'landlord.admin.tenants.delete';
    case TenantAccess = 'landlord.admin.tenants.system-access';

    // Users
    case UserList            = 'landlord.admin.users.list';
    case UserCreate          = 'landlord.admin.users.create';
    case UserEdit            = 'landlord.admin.users.edit';
    case UserDelete          = 'landlord.admin.users.delete';
    case UserImpersonate     = 'landlord.admin.users.impersonate';
    case UserStopImpersonate = 'landlord.admin.users.stop-impersonation';

    public function route(mixed ...$parameters): string
    {
        return route($this->value, $parameters);
    }
}
