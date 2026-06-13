<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Listeners\InvalidateUserRolesCache;
use App\Models\Tenant;
use App\Models\User;
use Mockery;
use Spatie\Permission\Events\RoleAttachedEvent;
use Spatie\Permission\Events\RoleDetachedEvent;
use Stancl\Tenancy\Tenancy;
use Tests\TestCase;

class InvalidateUserRolesCacheTest extends TestCase
{
    private function mockTenancy(bool $initialized, string $tenantKey = 'tenant-test'): void
    {
        $tenancy = Mockery::mock(Tenancy::class);
        $tenancy->initialized = $initialized;

        if ($initialized) {
            $tenant = Mockery::mock(Tenant::class);
            $tenant->shouldReceive('getTenantKey')->andReturn($tenantKey);
            $tenancy->tenant = $tenant;
        }

        $this->app->instance(Tenancy::class, $tenancy);
    }

    private function makeUser(int $id): User
    {
        $user = new User();
        $user->forceFill(['id' => $id]);
        return $user;
    }

    public function test_forgets_cache_on_role_attached(): void
    {
        $this->mockTenancy(true, 'tenant-abc');

        $user = $this->makeUser(42);
        cache()->put('user.42.roles.tenant.tenant-abc', collect(['admin']), now()->addMinutes(10));

        (new InvalidateUserRolesCache())->handle(new RoleAttachedEvent($user, [1]));

        $this->assertNull(cache()->get('user.42.roles.tenant.tenant-abc'));
    }

    public function test_forgets_cache_on_role_detached(): void
    {
        $this->mockTenancy(true, 'tenant-abc');

        $user = $this->makeUser(42);
        cache()->put('user.42.roles.tenant.tenant-abc', collect(['admin']), now()->addMinutes(10));

        (new InvalidateUserRolesCache())->handle(new RoleDetachedEvent($user, [1]));

        $this->assertNull(cache()->get('user.42.roles.tenant.tenant-abc'));
    }

    public function test_does_nothing_when_tenancy_not_initialized(): void
    {
        $this->mockTenancy(false);

        $user = $this->makeUser(1);
        cache()->put('user.1.roles.tenant.tenant-abc', collect(['admin']), now()->addMinutes(10));

        (new InvalidateUserRolesCache())->handle(new RoleAttachedEvent($user, [1]));

        $this->assertNotNull(cache()->get('user.1.roles.tenant.tenant-abc'));
    }

    public function test_does_nothing_for_non_user_model(): void
    {
        $this->mockTenancy(true, 'tenant-abc');

        $otherModel = new class extends \Illuminate\Database\Eloquent\Model {};
        $otherModel->forceFill(['id' => 99]);
        cache()->put('user.99.roles.tenant.tenant-abc', collect(['admin']), now()->addMinutes(10));

        (new InvalidateUserRolesCache())->handle(new RoleAttachedEvent($otherModel, [1]));

        $this->assertNotNull(cache()->get('user.99.roles.tenant.tenant-abc'));
    }

    public function test_only_invalidates_the_affected_user(): void
    {
        $this->mockTenancy(true, 'tenant-abc');

        cache()->put('user.42.roles.tenant.tenant-abc', collect(['admin']), now()->addMinutes(10));
        cache()->put('user.7.roles.tenant.tenant-abc', collect(['user']), now()->addMinutes(10));

        (new InvalidateUserRolesCache())->handle(new RoleAttachedEvent($this->makeUser(42), [2]));

        $this->assertNull(cache()->get('user.42.roles.tenant.tenant-abc'));
        $this->assertNotNull(cache()->get('user.7.roles.tenant.tenant-abc'));
    }
}
