<?php

declare(strict_types=1);

namespace Tests\Unit\Permission;

use App\Listeners\FlushPermissionCache;
use App\Models\Tenant;
use Mockery;
use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Events\TenancyInitialized;
use Stancl\Tenancy\Tenancy;
use Tests\TestCase;

class FlushPermissionCacheTest extends TestCase
{
    public function test_sets_tenant_specific_cache_key_on_tenancy_initialized(): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->cacheKey = 'spatie.permission.cache';

        $tenant = Mockery::mock(Tenant::class);
        $tenant->shouldReceive('getTenantKey')->andReturn('tenant-abc');

        $tenancy = Mockery::mock(Tenancy::class);
        $tenancy->tenant = $tenant;

        $event = Mockery::mock(TenancyInitialized::class);
        $event->tenancy = $tenancy;

        $listener = new FlushPermissionCache($registrar);
        $listener->handle($event);

        $this->assertSame('spatie.permission.cache.tenant.tenant-abc', $registrar->cacheKey);
    }

    public function test_cache_key_changes_between_different_tenants(): void
    {
        $registrar = app(PermissionRegistrar::class);

        foreach (['tenant-1', 'tenant-2', 'tenant-3'] as $tenantKey) {
            $tenant = Mockery::mock(Tenant::class);
            $tenant->shouldReceive('getTenantKey')->andReturn($tenantKey);

            $tenancy = Mockery::mock(Tenancy::class);
            $tenancy->tenant = $tenant;

            $event = Mockery::mock(TenancyInitialized::class);
            $event->tenancy = $tenancy;

            (new FlushPermissionCache($registrar))->handle($event);

            $this->assertSame("spatie.permission.cache.tenant.{$tenantKey}", $registrar->cacheKey);
        }
    }

    public function test_does_not_wipe_cache_store_on_tenant_switch(): void
    {
        // Pobla el cache store con un valor para el tenant
        $tenantKey = 'tenant-xyz';
        $storeKey = "spatie.permission.cache.tenant.{$tenantKey}";
        cache()->put($storeKey, ['fake' => 'permissions'], now()->addMinutes(5));

        $registrar = app(PermissionRegistrar::class);

        $tenant = Mockery::mock(Tenant::class);
        $tenant->shouldReceive('getTenantKey')->andReturn($tenantKey);

        $tenancy = Mockery::mock(Tenancy::class);
        $tenancy->tenant = $tenant;

        $event = Mockery::mock(TenancyInitialized::class);
        $event->tenancy = $tenancy;

        (new FlushPermissionCache($registrar))->handle($event);

        // El listener NO debe borrar la entrada del cache store
        $this->assertNotNull(cache()->get($storeKey), 'El listener no debe borrar el cache store del tenant');
    }
}
