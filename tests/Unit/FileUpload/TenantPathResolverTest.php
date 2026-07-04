<?php

namespace Tests\Unit\FileUpload;

use App\Common\Services\FileUpload\TenantPathResolver;
use Tests\TestCase;

class TenantPathResolverTest extends TestCase
{
    public function testResolvesLandlordPrefixWhenTenancyIsNotInitialized(): void
    {
        $resolver = new TenantPathResolver();

        $expected = 'landlord/activities/attachments/' . now()->format('Y/m');
        $this->assertSame($expected, $resolver->resolve('activities/attachments'));
    }

    public function testResolvesTenantPrefixWhenTenantIsActive(): void
    {
        $resolver = $this->makeResolverForTenant('42');

        $expected = 'tenant_42/activities/attachments/' . now()->format('Y/m');
        $this->assertSame($expected, $resolver->resolve('activities/attachments'));
    }

    public function testTrimsSlashesFromDirectory(): void
    {
        $resolver = $this->makeResolverForTenant('7');

        $expected = 'tenant_7/docs/' . now()->format('Y/m');
        $this->assertSame($expected, $resolver->resolve('/docs/'));
    }

    private function makeResolverForTenant(string $tenantId): TenantPathResolver
    {
        return new class($tenantId) extends TenantPathResolver {
            public function __construct(
                private readonly string $tenantId
            ) {
            }

            protected function getTenantId(): ?string
            {
                return $this->tenantId;
            }
        };
    }
}
