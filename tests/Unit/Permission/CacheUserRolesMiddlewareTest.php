<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Http\Middleware\CacheUserRoles;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Mockery;
use Stancl\Tenancy\Tenancy;
use Tests\TestCase;

class CacheUserRolesMiddlewareTest extends TestCase
{
    private function middleware(): CacheUserRoles
    {
        return new CacheUserRoles();
    }

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

    public function test_passes_through_when_tenancy_not_initialized(): void
    {
        $this->mockTenancy(false);

        $passed = false;
        $this->middleware()->handle(Request::create('/'), function () use (&$passed) {
            $passed = true;
            return response('');
        });

        $this->assertTrue($passed);
    }

    public function test_passes_through_when_no_authenticated_user(): void
    {
        $this->mockTenancy(true);

        $passed = false;
        $this->middleware()->handle(Request::create('/'), function () use (&$passed) {
            $passed = true;
            return response('');
        });

        $this->assertTrue($passed);
    }

    public function test_passes_through_when_roles_already_loaded(): void
    {
        $this->mockTenancy(true);

        $user = new User();
        $user->forceFill(['id' => 1]);
        $user->setRelation('roles', new Collection());
        $this->actingAs($user, 'web');

        $passed = false;
        $this->middleware()->handle(Request::create('/'), function () use (&$passed) {
            $passed = true;
            return response('');
        });

        $this->assertTrue($passed);
    }

    public function test_sets_relation_from_cache_on_hit(): void
    {
        $tenantKey = 'tenant-abc';
        $userId    = 42;

        $this->mockTenancy(true, $tenantKey);

        $cachedRoles = new Collection();
        cache()->put("user.{$userId}.roles.tenant.{$tenantKey}", $cachedRoles, now()->addMinutes(10));

        $user = new User();
        $user->forceFill(['id' => $userId]);
        $this->actingAs($user, 'web');

        $this->middleware()->handle(Request::create('/'), fn () => response(''));

        $this->assertTrue($user->relationLoaded('roles'));
        $this->assertSame($cachedRoles, $user->getRelation('roles'));
    }

    public function test_cache_key_is_specific_to_user_and_tenant(): void
    {
        $tenantKey = 'tenant-xyz';
        $userId    = 99;

        $this->mockTenancy(true, $tenantKey);

        // Populate a key for a DIFFERENT user — should NOT be used
        cache()->put("user.1.roles.tenant.{$tenantKey}", new Collection([['id' => 999]]), now()->addMinutes(10));

        $expectedRoles = new Collection();
        cache()->put("user.{$userId}.roles.tenant.{$tenantKey}", $expectedRoles, now()->addMinutes(10));

        $user = new User();
        $user->forceFill(['id' => $userId]);
        $this->actingAs($user, 'web');

        $this->middleware()->handle(Request::create('/'), fn () => response(''));

        $this->assertSame($expectedRoles, $user->getRelation('roles'));
    }
}
