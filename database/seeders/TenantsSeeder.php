<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantsSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            [
                'id' => 1,
                'domain' => 'a.localhost',
            ], // ajusta el dominio a tu entorno
            [
                'id' => 2,
                'domain' => 'b.localhost',
            ],
        ];

        foreach ($tenants as $data) {
            $tenant = Tenant::firstOrCreate([
                'id' => $data['id'],
            ]);
            $tenant->domains()
                ->firstOrCreate([
                    'domain' => $data['domain'],
                ]);
        }
    }
}
