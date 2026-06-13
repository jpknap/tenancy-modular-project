<?php

declare(strict_types=1);

namespace App\Listeners;

use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Events\TenancyInitialized;

class FlushPermissionCache
{
    public function __construct(private readonly PermissionRegistrar $registrar) {}

    public function handle(TenancyInitialized $event): void
    {
        // Solo redirige la clave al namespace del tenant.
        // No llamamos forgetCachedPermissions(): borraría la entrada del cache
        // store en cada request, inutilizando el TTL de 24h.
        // Con FPM, $this->permissions siempre arranca en null por proceso fresco.
        $this->registrar->cacheKey = 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->getTenantKey();
    }
}
