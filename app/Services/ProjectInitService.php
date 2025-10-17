<?php

namespace App\Services;

use App\ProjectManager;
use App\Projects\Landlord\LandlordProject;

class ProjectInitService
{
    public function init(): void
    {
        if (! tenancy()->initialized) {
            ProjectManager::setCurrentProject(new LandlordProject());
            ProjectManager::getCurrentProject()->init();
        }
    }
}
