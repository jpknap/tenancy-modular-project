<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // Permisos base
        $permissions = [
            'users:list',
            'users:create',
            'users:edit',
            'users:delete',
            'users:impersonate',
            'roles:assign',
            'settings:general',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Roles
        $superadmin = Role::firstOrCreate([
            'name' => 'superadmin',
            'guard_name' => 'web',
        ]);
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        // Permisos para admin (NO: users:impersonate, roles:assign — los asigna superadmin si quiere)
        $adminPermissions = ['users:list', 'users:create', 'users:edit', 'users:delete', 'settings:general'];
        $admin->syncPermissions($adminPermissions);
        // superadmin: NO asignar permisos — Gate::before lo bypasea todo
    }
}
