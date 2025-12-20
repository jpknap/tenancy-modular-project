<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\ProjectInterface;
use App\Models\Tenant;
use App\ProjectManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MigrateProjectDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Tenant $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle(): void
    {
        $this->tenant->run(function ($tenant) {
            $paths = $this->getMigrationPaths($tenant);

            Log::info("Iniciando migraciones para tenant: {$tenant->id} - Proyecto: {$tenant->current_project}");

            if (empty($paths)) {
                Log::warning("No se encontraron paths de migraciones para el tenant: {$tenant->id}");
                return;
            }

            foreach ($paths as $pathInfo) {
                $path = $pathInfo['path'];
                $type = $pathInfo['type'];

                if (file_exists($path)) {
                    Log::info("Ejecutando migraciones [{$type}] desde: {$path}");

                    $exitCode = Artisan::call('migrate', [
                        '--path' => $path,
                        '--force' => true,
                        '--realpath' => true,
                    ]);

                    if ($exitCode === 0) {
                        Log::info("Migraciones [{$type}] completadas exitosamente: {$path}");
                    } else {
                        Log::error("Error ejecutando migraciones [{$type}]: {$path}");
                    }
                } else {
                    Log::warning("Path de migraciones [{$type}] no existe: {$path}");
                }
            }

            Log::info("Migraciones completadas para tenant: {$tenant->id}");
        });
    }

    /**
     * Obtiene los paths de migraciones según el proyecto del tenant
     */
    protected function getMigrationPaths(Tenant $tenant): array
    {
        $paths = [];

        //Siempre incluir migraciones Common
        $commonPath = database_path('migrations/projects/Common');
        if (file_exists($commonPath)) {
            $paths[] = [
                'type' => 'Common',
                'path' => $commonPath,
            ];
        }

        // migraciones del proyecto específico si está definido
        if (!empty($tenant->current_project)) {
            $projectPath = $this->getProjectMigrationPath($tenant->current_project);
            if ($projectPath && file_exists($projectPath)) {
                $paths[] = [
                    'type' => $tenant->current_project,
                    'path' => $projectPath,
                ];
            }
        }

        return $paths;
    }

    /**
     * Obtiene el path de migraciones para un proyecto específico
     */
    protected function getProjectMigrationPath(string $projectPrefix): ?string
    {
        $projects = ProjectManager::getProjects();

        /** @var class-string<ProjectInterface> $projectClass */
        foreach ($projects as $projectClass) {
            if ($projectClass::getPrefix() === $projectPrefix) {
                $projectInstance = new $projectClass();
                $migrationFolder = $projectInstance->getPathMigration();

                if (!empty($migrationFolder)) {
                    return database_path("migrations/projects/{$migrationFolder}");
                }
            }
        }

        return null;
    }
}
