<?php

namespace App\Projects\Landlord\Http\Controller\Admin;

use App\Attributes\Middleware;
use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Common\Audit\Models\Activity;

#[RoutePrefix('admin/audit')]
#[Middleware(['auth.landlord'])]
class AuditController
{
    #[Route('', methods: ['GET'], name: 'list')]
    public function list(Request $request): View
    {
        $tenants = Tenant::all();
        $audits = collect();
        $selectedTenant = null;

        if ($tenantId = $request->query('tenant_id')) {
            $selectedTenant = Tenant::findOrFail($tenantId);
            $selectedTenant->run(function () use (&$audits) {
                $audits = Activity::with('causer')
                    ->latest()
                    ->paginate(25);
            });
        }

        return view('landlord.admin.audit.list', compact('tenants', 'audits', 'selectedTenant'));
    }

    #[Route('{tenantId}/{id}', methods: ['GET'], name: 'show')]
    public function show(int $tenantId, int $id): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        $audit = null;

        $tenant->run(function () use ($id, &$audit) {
            $audit = Activity::with('causer')->findOrFail($id);
        });

        return view('landlord.admin.audit.show', compact('tenant', 'audit'));
    }
}
