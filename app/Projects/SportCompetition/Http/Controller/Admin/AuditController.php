<?php

namespace App\Projects\SportCompetition\Http\Controller\Admin;

use App\Attributes\Middleware;
use App\Attributes\Route;
use App\Attributes\RoutePrefix;
use Illuminate\View\View;
use App\Common\Audit\Models\Activity;

#[RoutePrefix('admin/audit')]
#[Middleware(['auth.admin'])]
class AuditController
{
    #[Route('', methods: ['GET'], name: 'list')]
    public function list(): View
    {
        $audits = Activity::with('causer')
            ->latest()
            ->paginate(25);

        return view('sport-competition.admin.audit.list', compact('audits'));
    }

    #[Route('{id}', methods: ['GET'], name: 'show')]
    public function show(int $id): View
    {
        $audit = Activity::with('causer')->findOrFail($id);

        return view('sport-competition.admin.audit.show', compact('audit'));
    }
}
