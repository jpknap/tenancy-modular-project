<?php

namespace App\Services;

use App\Common\Services\LocaleService;
use App\Models\Tenant;
use App\ProjectManager;
use App\Projects\Landlord\LandlordProject;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectInitService
{
    public function init(): void
    {
        Log::debug('[Log-System-Auth] ProjectInitService::init()', [
            'tenancy_initialized' => tenancy()->initialized,
        ]);

        if (! tenancy()->initialized) {
            Log::debug('[Log-System-Auth] Tenancia no inicializada, usando LandlordProject');
            ProjectManager::setCurrentProject(new LandlordProject());
            ProjectManager::getCurrentProject()->init();
            $this->applyLocale(null);
            return;
        }

        /** @var Tenant $tenant */
        $tenant = tenancy()
            ->tenant;
        Log::debug('[Log-System-Auth] Tenancia inicializada', [
            'tenant_id' => $tenant->id,
            'tenant_domain' => $tenant->domain,
            'tenant_current_project' => $tenant->current_project,
        ]);

        $project = ProjectManager::getProject(prefix: $tenant->current_project);

        Log::debug('[Log-System-Auth] Proyecto obtenido desde ProjectManager', [
            'project_class' => $project,
        ]);

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

        Log::debug('[Log-System-Auth] Locale aplicado', [
            'locale' => $locale,
            'from_session' => ! ! session('locale'),
            'from_user' => $this->resolveUserLocale() ? 'yes' : 'no',
            'from_tenant' => ! ! $tenantLocale,
        ]);

        if (in_array($locale, LocaleService::SUPPORTED, true)) {
            App::setLocale($locale);
        }
    }

    private function resolveUserLocale(): ?string
    {
        foreach (array_keys(config('auth.guards', [])) as $guard) {
            $user = Auth::guard($guard)->user();
            if ($user && ! empty($user->locale)) {
                Log::debug('[Log-System-Auth] Locale del usuario encontrado', [
                    'guard' => $guard,
                    'user_id' => $user->id,
                    'locale' => $user->locale,
                ]);
                return $user->locale;
            }
        }

        return null;
    }
}
