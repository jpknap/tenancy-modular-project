<?php

namespace App\Http\Middleware;

use App\Services\ProjectInitService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectInitialized
{
    public function __construct(
        private ProjectInitService $projectInitService
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        Log::debug('[Log-System-Auth] ProjectInitialized::handle()', [
            'path' => $request->path(),
            'host' => $request->getHost(),
        ]);

        $this->projectInitService->init();

        Log::debug('[Log-System-Auth] ProjectInitialized::handle() completado');

        return $next($request);
    }
}
