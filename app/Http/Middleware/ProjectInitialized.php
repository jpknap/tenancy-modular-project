<?php

namespace App\Http\Middleware;

use App\Services\ProjectInitService;
use Closure;
use Illuminate\Http\Request;

class ProjectInitialized
{
    public function __construct(
        private ProjectInitService $projectInitService
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $this->projectInitService->init();
        return $next($request);
    }
}
