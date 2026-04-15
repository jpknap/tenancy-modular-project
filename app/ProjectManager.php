<?php

namespace App;

use App\Contracts\ProjectInterface;
use App\Projects\ActivitiesBoard\ActivitiesBoardProject;
use App\Projects\Landlord\LandlordProject;
use App\Projects\SportCompetition\SportCompetitionProject;
use Exception;
use Illuminate\Support\Facades\Log;

class ProjectManager
{
    private static ?ProjectInterface $currentProject = null;

    /**
     * @var class-string<ProjectInterface>[]
     */
    private static array $projects = [
        LandlordProject::class,
        ActivitiesBoardProject::class,
        SportCompetitionProject::class,
    ];

    public static function getProjects(): array
    {
        return self::$projects;
    }

    public static function getProject(string $prefix): string
    {
        Log::debug('[Log-System-Auth] ProjectManager::getProject()', [
            'prefix' => $prefix,
        ]);
        foreach (self::$projects as $project) {
            if ($project::getPrefix() === $prefix) {
                Log::debug('[Log-System-Auth] Proyecto encontrado', [
                    'prefix' => $prefix,
                    'project_class' => $project,
                ]);
                return $project;
            }
        }
        Log::error('[Log-System-Auth] Proyecto no encontrado', [
            'prefix' => $prefix,
            'available_projects' => array_map(fn ($p) => $p::getPrefix(), self::$projects),
        ]);
        throw new Exception('Project not found');
    }

    public static function setCurrentProject(ProjectInterface $project): void
    {
        Log::debug('[Log-System-Auth] ProjectManager::setCurrentProject()', [
            'project_class' => get_class($project),
            'project_prefix' => $project::getPrefix(),
            'already_set' => self::$currentProject !== null,
        ]);
        if (self::$currentProject != null) {
            Log::debug('[Log-System-Auth] Proyecto ya estaba configurado, ignorando nuevo', [
                'existing_project' => get_class(self::$currentProject),
                'attempted_project' => get_class($project),
            ]);
            return;
        }
        self::$currentProject = $project;
    }

    public static function getCurrentProject(): ?ProjectInterface
    {
        Log::debug('[Log-System-Auth] ProjectManager::getCurrentProject()', [
            'project_class' => self::$currentProject ? get_class(self::$currentProject) : 'null',
            'project_prefix' => self::$currentProject ? self::$currentProject::getPrefix() : 'null',
        ]);
        return self::$currentProject;
    }
}
