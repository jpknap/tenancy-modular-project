<?php

namespace App;

use App\Contracts\ProjectInterface;
use App\Projects\Landlord\LandlordProject;

class ProjectManager
{
    private static ?ProjectInterface $currentProject = null;

    /**
     * @var class-string<ProjectInterface>[]
     */
    private static array $projects = [LandlordProject::class];

    public static function getProjects(): array
    {
        return self::$projects;
    }

    public static function setCurrentProject(ProjectInterface $project): void
    {
        self::$currentProject = $project;
    }

    public static function getCurrentProject(): ?ProjectInterface
    {
        return self::$currentProject;
    }
}
