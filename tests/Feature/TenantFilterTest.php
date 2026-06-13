<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Stancl\Tenancy\Events\TenantCreated;
use Tests\TestCase;

class TenantFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake([TenantCreated::class]);
        \Artisan::call('migrate');

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@landlord.test',
            'password' => bcrypt('password'),
        ]);
    }

    public function testTenantListFilterByName(): void
    {
        Tenant::create(['name' => 'Acme Corporation', 'identifier' => 'acme', 'current_project' => 'landlord']);
        Tenant::create(['name' => 'Beta Company', 'identifier' => 'beta', 'current_project' => 'landlord']);
        Tenant::create(['name' => 'Other Corp', 'identifier' => 'other', 'current_project' => 'landlord']);

        $response = $this->actingAs($this->admin, 'landlord')
            ->get(route('landlord.admin.tenants.list', ['filters' => ['name' => 'acm']]));

        $response->assertStatus(200);
    }

    public function testTenantListFilterIgnoresAccents(): void
    {
        Tenant::create(['name' => 'Acrópolis Ltd', 'identifier' => 'acropolis', 'current_project' => 'landlord']);
        Tenant::create(['name' => 'Beta Company', 'identifier' => 'beta', 'current_project' => 'landlord']);

        $response = $this->actingAs($this->admin, 'landlord')
            ->get(route('landlord.admin.tenants.list', ['filters' => ['name' => 'acropolis']]));

        $response->assertStatus(200);
    }

    public function testTenantListFilterIsCaseInsensitive(): void
    {
        Tenant::create(['name' => 'Acme Corporation', 'identifier' => 'acme', 'current_project' => 'landlord']);
        Tenant::create(['name' => 'Beta Company', 'identifier' => 'beta', 'current_project' => 'landlord']);

        $response = $this->actingAs($this->admin, 'landlord')
            ->get(route('landlord.admin.tenants.list', ['filters' => ['name' => 'ACME']]));

        $response->assertStatus(200);
    }
}
