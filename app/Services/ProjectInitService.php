<?php

namespace App\Services;

use App\Models\Tenant;
use App\ProjectManager;
use App\Projects\Landlord\LandlordProject;

class ProjectInitService
{
    public function init(): void
    {
        if (!tenancy()->initialized) {
            ProjectManager::setCurrentProject(new LandlordProject());
            ProjectManager::getCurrentProject()->init();
            return;
        }
        /** @var Tenant $tenant */
        $tenant = tenancy()->tenant;
        $project = ProjectManager::getProject(prefix: $tenant->current_project);

        ProjectManager::setCurrentProject(new $project());
        ProjectManager::getCurrentProject()->init();
    }
}
