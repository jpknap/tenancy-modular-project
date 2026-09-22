<?php

namespace Tests\Feature;

use App\Contracts\ProjectInterface;
use App\ProjectManager;
use App\Projects\TicketsValdi\TicketsValdiProject;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TicketsValdiProjectTest extends TestCase
{
    #[Test]
    public function itIsRegisteredInProjectManager(): void
    {
        $this->assertContains(TicketsValdiProject::class, ProjectManager::getProjects());
    }

    #[Test]
    public function itIsResolvableByItsPrefix(): void
    {
        $this->assertSame(TicketsValdiProject::class, ProjectManager::getProject('tickets-valdi'));
    }

    #[Test]
    public function itExposesPrefixAndTitle(): void
    {
        $this->assertSame('tickets-valdi', TicketsValdiProject::getPrefix());
        $this->assertSame('Tickets Valdi', TicketsValdiProject::getTitle());
    }

    #[Test]
    public function itIsSelectableAsTenantProject(): void
    {
        $selectable = [];

        /** @var class-string<ProjectInterface> $projectClass */
        foreach (ProjectManager::getProjects() as $projectClass) {
            if ($projectClass::getPrefix() !== 'landlord') {
                $selectable[$projectClass::getPrefix()] = $projectClass::getTitle();
            }
        }

        $this->assertArrayHasKey('tickets-valdi', $selectable);
        $this->assertSame('Tickets Valdi', $selectable['tickets-valdi']);
    }

    #[Test]
    public function itIsRegisteredInProjectsConfig(): void
    {
        $config = config('projects.tickets-valdi');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('admins', $config);
        $this->assertArrayHasKey('controllers', $config);
    }

    #[Test]
    public function itHasItsOwnMigrationFolder(): void
    {
        $project = new TicketsValdiProject();

        $this->assertSame('TicketsValdi', $project->getPathMigration());
        $this->assertDirectoryExists(database_path('migrations/projects/TicketsValdi'));
    }

    #[Test]
    public function itHasItsOwnDocsFolder(): void
    {
        $this->assertDirectoryExists(app_path('Projects/TicketsValdi/docs'));
    }

    #[Test]
    public function itPointsToItsOwnLangPath(): void
    {
        $project = new TicketsValdiProject();

        $this->assertSame(lang_path('projects/tickets-valdi'), $project->getLangPath());
        $this->assertDirectoryExists($project->getLangPath());
    }

    #[Test]
    public function itBuildsEndpointsFromItsControllers(): void
    {
        $endpoints = TicketsValdiProject::getEndpoints();

        $this->assertNotEmpty($endpoints);

        $names = array_map(fn ($endpoint) => $endpoint->name, $endpoints);

        $this->assertContains('tickets-valdi.auth.login', $names);
        $this->assertContains('tickets-valdi.admin.users.list', $names);
    }

    #[Test]
    public function itsEndpointsAreRegisteredAsApplicationRoutes(): void
    {
        foreach (TicketsValdiProject::getEndpoints() as $endpoint) {
            $this->assertTrue(
                Route::has($endpoint->name),
                "La ruta '{$endpoint->name}' no está registrada en la aplicación."
            );
        }
    }
}
