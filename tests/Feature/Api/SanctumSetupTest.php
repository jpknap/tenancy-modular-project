<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\CreatesTenants;
use Tests\TestCase;

class SanctumSetupTest extends TestCase
{
    // Sin RefreshDatabase: sus transacciones no conviven con los reconnects
    // de tenancy sobre sqlite de archivo. Se usa migrate:fresh explícito.
    use CreatesTenants;

    protected function setUp(): void
    {
        parent::setUp();
        \Artisan::call('migrate:fresh');
    }

    #[Test]
    public function personalAccessTokensTableExistsInCentral(): void
    {
        $this->assertTrue(Schema::hasTable('personal_access_tokens'));
    }

    #[Test]
    public function personalAccessTokensTableExistsInTenant(): void
    {
        $tenant = $this->createTenant('acme', 'activities-board');

        $tenant->run(function () {
            $this->assertTrue(Schema::hasTable('personal_access_tokens'));
        });
    }

    #[Test]
    public function createTokenPersistsRowAndReturnsPlainText(): void
    {
        $user = User::create([
            'name' => 'Central User',
            'email' => 'central@test.com',
            'password' => bcrypt('secret'),
        ]);

        $newToken = $user->createToken('test-device');

        $this->assertNotEmpty($newToken->plainTextToken);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    #[Test]
    public function tokenCreatedInTenantDoesNotExistInOtherTenantNorCentral(): void
    {
        $tenantA = $this->createTenant('tenant-a', 'activities-board');
        $tenantB = $this->createTenant('tenant-b', 'activities-board');

        $tenantA->run(function () {
            $user = User::create([
                'name' => 'Tenant A User',
                'email' => 'user@tenant-a.test',
                'password' => bcrypt('secret'),
            ]);
            $user->createToken('device-a');

            $this->assertSame(1, DB::table('personal_access_tokens')->count());
        });

        $tenantB->run(function () {
            $this->assertSame(0, DB::table('personal_access_tokens')->count());
        });

        $this->assertSame(0, DB::table('personal_access_tokens')->count());
    }
}
