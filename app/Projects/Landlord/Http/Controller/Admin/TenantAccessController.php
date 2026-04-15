<?php

namespace App\Projects\Landlord\Http\Controller\Admin;

use App\Attributes\Middleware;
use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

#[RoutePrefix('admin/tenants')]
#[Middleware(['auth.landlord'])]
class TenantAccessController extends Controller
{
    #[Route('{id}/system-access', methods: ['GET'], name: 'system-access')]
    public function access(int $id): RedirectResponse
    {
        abort_unless(auth('landlord')->user()?->hasRole('superadmin'), 403);

        $tenant = Tenant::findOrFail($id);

        $domainRecord = $tenant->domains->first();
        abort_if($domainRecord === null, 422, 'El tenant no tiene dominio registrado.');

        $userId = null;

        $tenant->run(function () use (&$userId) {
            $user = User::where('is_system_user', true)->first();
            abort_if($user === null, 404, 'El tenant no tiene usuario sistema.');
            $userId = $user->id;
        });

        $expires = now()->addMinutes(2)->timestamp;
        $payload = "{$userId}:{$expires}";
        $sig = hash_hmac('sha256', $payload, config('app.key'));

        $scheme = str_starts_with(config('app.url', 'http://'), 'https') ? 'https' : 'http';
        $tenantUrl = "{$scheme}://{$domainRecord->domain}/system-login?" . http_build_query([
            'uid' => $userId,
            'exp' => $expires,
            'sig' => $sig,
        ]);

        return redirect($tenantUrl);
    }
}
