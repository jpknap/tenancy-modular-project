<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogTenancyState
{
    public function handle(Request $request, Closure $next)
    {
        Log::debug('[Log-System-Auth] LogTenancyState::handle() ANTES', [
            'path' => $request->path(),
            'host' => $request->getHost(),
            'tenancy_initialized' => tenancy()->initialized,
            'tenant_id' => tenancy()->tenant?->id,
            'tenant_domain' => tenancy()->tenant?->domain,
        ]);

        $response = $next($request);

        Log::debug('[Log-System-Auth] LogTenancyState::handle() DESPUÉS', [
            'path' => $request->path(),
            'host' => $request->getHost(),
            'tenancy_initialized' => tenancy()->initialized,
            'tenant_id' => tenancy()->tenant?->id,
            'tenant_domain' => tenancy()->tenant?->domain,
        ]);

        return $response;
    }
}
