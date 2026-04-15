<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantSystemUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'system@system.internal'],
            [
                'name'           => 'System',
                'password'       => Hash::make(Str::random(32)),
                'is_system_user' => true,
            ]
        );

        $user->assignRole('superadmin');
    }
}
