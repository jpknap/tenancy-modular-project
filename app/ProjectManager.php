<?php

namespace App;

use App\Contracts\ProjectInterface;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;
use App\Projects\Landlord\LandlordProject;
use Exception;

class ProjectManager
{
    private static ?ProjectInterface $currentProject = null;

    /**
     * @var class-string<ProjectInterface>[]
     */
    private static array $projects = [
        LandlordProject::class,
        ActivitiesBoardProject::class,
    ];

    public static function getProjects(): array
    {
        return self::$projects;
    }

    /**
     * @throws Exception
     */
    public static function getProject(string $prefix): string
    {
        foreach (self::$projects as $project) {
            if ($project::getPrefix() === $prefix) {
                return $project;
            }
        }
        throw new Exception("Project not found");
    }

    public static function setCurrentProject(ProjectInterface $project): void
    {
        if(null != self::$currentProject) {
            return;
        }
        self::$currentProject = $project;
    }

    public static function getCurrentProject(): ?ProjectInterface
    {
        return self::$currentProject;
    }
}
