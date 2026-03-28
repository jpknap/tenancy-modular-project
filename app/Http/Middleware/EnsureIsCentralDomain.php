<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureIsCentralDomain
{
    public function handle(Request $request, Closure $next)
    {
        $centralDomains = Config::get('tenancy.central_domains', []);
        $host = $request->getHost();

        if (!in_array($host, $centralDomains)) {
            abort(404);
        }

        return $next($request);
    }
}
