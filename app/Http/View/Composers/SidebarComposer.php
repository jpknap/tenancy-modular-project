<?php

namespace App\Http\View\Composers;

use App\ProjectManager;
use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view): void
    {
        $project = ProjectManager::getCurrentProject();
        $view->with('menuBuilder', $project->getMenuBuilder());
    }
}
