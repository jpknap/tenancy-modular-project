<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class TenantUsersSeeder extends Seeder
{
    public function run(): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Tenant> $tenants */
        $tenants = Tenant::query()->get();
        $tenants
            ->each(function (Tenant $tenant, int $key): void {
                $tenant->run(function (): void {
                    (new RolesAndPermissionsSeeder())->run();

                    $testUser = User::factory()->create([
                        'name' => 'Test User',
                        'email' => 'test@example.com',
                    ]);
                    $testUser->assignRole('superadmin');

                    User::factory()->count(8)->create();
                });
            });
    }
}
