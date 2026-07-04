<?php

namespace App\Common\Services\FileUpload;

/**
 * Resuelve el directorio de destino de un archivo, aislado por tenant.
 *
 * Formato: {tenant_prefix}/{directory}/{año}/{mes}
 *   ej tenant:   tenant_3/activities/attachments/2026/07
 *   ej landlord: landlord/activities/attachments/2026/07
 */
class TenantPathResolver
{
    public function resolve(string $directory): string
    {
        return sprintf('%s/%s/%s', $this->getTenantPrefix(), trim($directory, '/'), now() ->format('Y/m'));
    }

    protected function getTenantPrefix(): string
    {
        $tenantId = $this->getTenantId();

        return $tenantId !== null ? "tenant_{$tenantId}" : 'landlord';
    }

    protected function getTenantId(): ?string
    {
        if (! tenancy()->initialized) {
            return null;
        }

        return (string) tenant('id');
    }
}
