<?php

namespace Tests\Support;

use App\Models\Tenant;

/**
 * Crea tenants reales (DB sqlite propia + migraciones Common/proyecto)
 * para tests de aislamiento multi-tenant.
 */
trait CreatesTenants
{
    /**
     * @var string[] rutas de archivos sqlite creados, para limpieza
     */
    protected array $tenantDatabases = [];

    protected function tearDown(): void
    {
        foreach ($this->tenantDatabases as $database) {
            if (file_exists($database)) {
                unlink($database);
            }
        }
        $this->tenantDatabases = [];

        parent::tearDown();
    }

    protected function configureSqliteTenancy(): void
    {
        config([
            'tenancy.database.template_tenant_connection' => 'sqlite',
            'tenancy.database.suffix' => '.sqlite',
        ]);
    }

    protected function createTenant(string $identifier, string $project, ?string $domain = null): Tenant
    {
        $this->configureSqliteTenancy();

        /** @var Tenant $tenant */
        $tenant = Tenant::create([
            'name' => ucfirst($identifier),
            'identifier' => $identifier,
            'current_project' => $project,
        ]);

        if ($domain !== null) {
            $tenant->domains()
                ->create([
                    'domain' => $domain,
                ]);
        }

        $this->tenantDatabases[] = database_path($tenant->database()->getName());

        return $tenant;
    }
}
