<?php

namespace App;

use App\Contracts\ProjectInterface;

class ProjectManager
{
    private static ?ProjectInterface $currentProject = null;

    public static function setCurrentProject(ProjectInterface $project): void
    {
        self::$currentProject = $project;
    }

    public static function getCurrentProject(): ?ProjectInterface
    {
        return self::$currentProject;
    }
}
