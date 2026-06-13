<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CacheUserRoles
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! tenancy()->initialized) {
            return $next($request);
        }

        /** @var Model|null $user */
        $user = collect(array_keys(config('auth.guards', [])))
            ->map(fn (string $guard) => auth()->guard($guard)->user())
            ->filter()
            ->first();

        if (! $user instanceof Model || $user->relationLoaded('roles')) {
            return $next($request);
        }

        $tenantKey = tenancy()->tenant->getTenantKey();
        $cacheKey  = "user.{$user->id}.roles.tenant.{$tenantKey}";

        $roles = cache()->remember(
            $cacheKey,
            now()->addMinutes(10),
            fn () => $user->roles()->with('permissions')->get()
        );

        $user->setRelation('roles', $roles);

        return $next($request);
    }
}
