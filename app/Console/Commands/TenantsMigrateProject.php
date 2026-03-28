<?php

namespace App\Console\Commands;

use App\Contracts\ProjectInterface;
use App\Models\Tenant;
use App\ProjectManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TenantsMigrateProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:migrate-project 
                            {--tenant=* : IDs específicos de tenants}
                            {--project= : Migrar solo tenants de un proyecto específico}
                            {--common : Ejecutar solo migraciones Common}
                            {--fresh : Hacer fresh antes de migrar}
                            {--seed : Ejecutar seeders después de migrar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta migraciones de proyecto (Common + específicas) en tenants existentes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenantIds = $this->option('tenant');
        $projectFilter = $this->option('project');
        $commonOnly = $this->option('common');
        $fresh = $this->option('fresh');
        $seed = $this->option('seed');

        // Obtener tenants
        $tenants = $this->getTenants($tenantIds, $projectFilter);

        if ($tenants->isEmpty()) {
            $this->error('No se encontraron tenants con los criterios especificados');
            return Command::FAILURE;
        }

        $this->info("Se ejecutarán migraciones en {$tenants->count()} tenant(s)");
        $this->newLine();

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant, $commonOnly, $fresh, $seed);
        }

        $this->newLine();
        $this->info('✅ Proceso de migraciones completado');

        return Command::SUCCESS;
    }

    /**
     * Obtiene los tenants según los filtros
     */
    protected function getTenants(?array $tenantIds, ?string $projectFilter)
    {
        $query = Tenant::query();

        if (!empty($tenantIds)) {
            $query->whereIn('id', $tenantIds);
        }

        if ($projectFilter) {
            $query->where('current_project', $projectFilter);
        }

        return $query->get();
    }

    /**
     * Procesa un tenant
     */
    protected function processTenant(Tenant $tenant, bool $commonOnly, bool $fresh, bool $seed): void
    {
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("📦 Tenant: {$tenant->name} (ID: {$tenant->id})");
        $this->line("   Proyecto: " . ($tenant->current_project ?? 'Sin asignar'));
        $this->newLine();

        $tenant->run(function ($tenant) use ($commonOnly, $fresh, $seed) {
            // Fresh si se solicitó
            if ($fresh) {
                $this->warn('⚠️  Ejecutando migrate:fresh...');
                Artisan::call('migrate:fresh', ['--force' => true]);
                $this->line('   ✓ Fresh completado');
            }

            $paths = $this->getMigrationPaths($tenant, $commonOnly);

            if (empty($paths)) {
                $this->warn('   ⚠️  No se encontraron paths de migraciones');
                return;
            }

            foreach ($paths as $pathInfo) {
                $this->migratePath($pathInfo['type'], $pathInfo['path']);
            }

            // Seed si se solicitó
            if ($seed) {
                $this->info('   🌱 Ejecutando seeders...');
                Artisan::call('db:seed', ['--force' => true]);
                $this->line('   ✓ Seeders completados');
            }
        });

        $this->info("✅ Tenant {$tenant->id} completado");
        $this->newLine();
    }

    /**
     * Ejecuta migraciones en un path
     */
    protected function migratePath(string $type, string $path): void
    {
        if (!file_exists($path)) {
            $this->warn("   ⚠️  Path [{$type}] no existe: {$path}");
            return;
        }

        $files = glob($path . '/*.php');
        $count = count($files);

        $this->line("   🔄 Migrando [{$type}] ({$count} archivo(s))...");
        
        $exitCode = Artisan::call('migrate', [
            '--path' => $path,
            '--force' => true,
            '--realpath' => true,
        ]);

        if ($exitCode === 0) {
            $output = Artisan::output();
            
            // Mostrar solo mensajes importantes
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (str_contains($line, 'Migrating') || str_contains($line, 'Migrated')) {
                    $this->line("      " . trim($line));
                }
            }
            
            $this->info("   ✓ [{$type}] completado");
        } else {
            $this->error("   ✗ Error en [{$type}]");
        }
    }

    /**
     * Obtiene los paths de migraciones
     */
    protected function getMigrationPaths(Tenant $tenant, bool $commonOnly): array
    {
        $paths = [];
        
        // 1. Siempre incluir migraciones Common
        $commonPath = database_path('migrations/projects/Common');
        if (file_exists($commonPath)) {
            $paths[] = [
                'type' => 'Common',
                'path' => $commonPath,
            ];
        }

        // 2. Si no es commonOnly, incluir migraciones del proyecto específico
        if (!$commonOnly && !empty($tenant->current_project)) {
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
