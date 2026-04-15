<?php

namespace App\Services;

use App\Common\Services\LocaleService;
use App\Models\Tenant;
use App\ProjectManager;
use App\Projects\Landlord\LandlordProject;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ProjectInitService
{
    public function init(): void
    {
        if (!tenancy()->initialized) {
            ProjectManager::setCurrentProject(new LandlordProject());
            ProjectManager::getCurrentProject()->init();
            $this->applyLocale(null);
            return;
        }

        /** @var Tenant $tenant */
        $tenant = tenancy()->tenant;
        $project = ProjectManager::getProject(prefix: $tenant->current_project);

        ProjectManager::setCurrentProject(new $project());
        ProjectManager::getCurrentProject()->init();

        $this->applyLocale($tenant->locale);
    }

    private function applyLocale(?string $tenantLocale): void
    {
        $locale = session('locale')
            ?? $this->resolveUserLocale()
            ?? $tenantLocale
            ?? config('app.locale', 'es');

        if (in_array($locale, LocaleService::SUPPORTED, true)) {
            App::setLocale($locale);
        }
    }

    private function resolveUserLocale(): ?string
    {
        foreach (array_keys(config('auth.guards', [])) as $guard) {
            $user = Auth::guard($guard)->user();
            if ($user && ! empty($user->locale)) {
                return $user->locale;
            }
        }

        return null;
    }
}
