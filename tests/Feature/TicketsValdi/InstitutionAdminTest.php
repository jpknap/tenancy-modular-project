<?php

namespace Tests\Feature\TicketsValdi;

use App\ProjectManager;
use App\Projects\TicketsValdi\Models\Institution;
use App\Projects\TicketsValdi\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tests\Support\CreatesTenants;
use Tests\TestCase;

/**
 * CRUD de Institution vía el admin del proyecto (list, create, edit), ejercitado
 * a través de un dominio real de tenant.
 *
 * No se puede probar esto pegándole a un path central (localhost/tickets-valdi/...):
 * TicketsValdiProject::getEndpoints() se registra tanto en routes/web.php (grupo
 * central) como en routes/tenant.php (grupo tenant), con la MISMA URI. La
 * RouteCollection de Laravel indexa por método+URI, así que el segundo registro
 * (tenant.php, cargado después vía TenancyServiceProvider::mapRoutes) pisa al
 * primero — el registro "central" queda inalcanzable en la práctica para
 * cualquier proyecto que aparezca en ambos archivos (SportCompetition,
 * ActivitiesBoard, TicketsValdi). Es un bug de la plataforma, no de este
 * proyecto; no se corrige acá porque routes/web.php y routes/tenant.php son
 * compartidos. Por eso el test entra por el único camino realmente alcanzable:
 * un dominio de tenant, igual que ActivityApiTest.
 *
 * Sin RefreshDatabase: ver comentario equivalente en ApiAuthTest.
 */
class InstitutionAdminTest extends TestCase
{
    use CreatesTenants;

    protected function setUp(): void
    {
        parent::setUp();
        \Artisan::call('migrate:fresh');

        // ProjectManager::$currentProject es estático y solo se setea una vez
        // por proceso PHP (ver setCurrentProject: ignora llamadas si ya hay
        // un proyecto activo). Correr `php artisan test` completo ejecuta
        // TODOS los tests en el mismo proceso, así que un test anterior de
        // otro proyecto (SportCompetition, ActivitiesBoard, Landlord) deja
        // el estático apuntando a esa clase. Sin resetear acá,
        // ProjectInitService::init() de esta suite queda pisado y el
        // TicketsValdiServiceProvider real nunca se registra: se rompe con
        // "Repository ... is not registered" aunque el test pase aislado.
        //
        // Es el mismo bug ya documentado en TenantFilterTest (contaminación
        // de estado estático); no se corrige en App\ProjectManager porque es
        // un archivo compartido fuera de este proyecto. El reset por
        // reflexión queda contenido acá, no en la clase compartida.
        $property = (new ReflectionClass(ProjectManager::class))->getProperty('currentProject');
        $property->setAccessible(true);
        $property->setValue(null, null);
    }

    #[Test]
    public function itListsInstitutions(): void
    {
        $domain = 'list-institutions.localhost';
        $tenant = $this->createTenant('list-institutions', 'tickets-valdi', $domain);

        $tenant->run(function () {
            $this->createAdminUser();
            Institution::create([
                'name' => 'Teatro Municipal',
            ]);
        });

        $response = $this->actingAs($this->findAdminUser($tenant), 'web')
            ->get("http://{$domain}/tickets-valdi/admin/institutions/list");

        $response->assertStatus(200)
            ->assertSee('Teatro Municipal');
    }

    #[Test]
    public function itShowsTheCreateForm(): void
    {
        $domain = 'create-form-institutions.localhost';
        $tenant = $this->createTenant('create-form-institutions', 'tickets-valdi', $domain);

        $tenant->run(function () {
            $this->createAdminUser();
        });

        $response = $this->actingAs($this->findAdminUser($tenant), 'web')
            ->get("http://{$domain}/tickets-valdi/admin/institutions/create");

        $response->assertStatus(200);
    }

    #[Test]
    public function itCreatesAnInstitution(): void
    {
        $domain = 'create-institutions.localhost';
        $tenant = $this->createTenant('create-institutions', 'tickets-valdi', $domain);

        $tenant->run(function () {
            $this->createAdminUser();
        });

        $response = $this->actingAs($this->findAdminUser($tenant), 'web')
            ->post("http://{$domain}/tickets-valdi/admin/institutions/create", [
                'name' => 'Teatro Municipal',
                'description' => 'Sala principal',
                'logo_url' => 'https://example.test/logo.png',
                'enabled' => '1',
            ]);

        $response->assertRedirect("http://{$domain}/tickets-valdi/admin/institutions/list");

        $tenant->run(function () {
            $this->assertDatabaseHas('institutions', [
                'name' => 'Teatro Municipal',
                'description' => 'Sala principal',
                'enabled' => true,
            ]);
        });
    }

    #[Test]
    public function nameIsRequiredToCreate(): void
    {
        $domain = 'required-name-institutions.localhost';
        $tenant = $this->createTenant('required-name-institutions', 'tickets-valdi', $domain);

        $tenant->run(function () {
            $this->createAdminUser();
        });

        $response = $this->actingAs($this->findAdminUser($tenant), 'web')
            ->post("http://{$domain}/tickets-valdi/admin/institutions/create", [
                'description' => 'Sin nombre',
            ]);

        $response->assertSessionHasErrors('name');

        $tenant->run(function () {
            $this->assertDatabaseMissing('institutions', [
                'description' => 'Sin nombre',
            ]);
        });
    }

    #[Test]
    public function itUpdatesAnInstitution(): void
    {
        $domain = 'update-institutions.localhost';
        $tenant = $this->createTenant('update-institutions', 'tickets-valdi', $domain);

        $institutionId = null;
        $tenant->run(function () use (&$institutionId) {
            $this->createAdminUser();
            $institutionId = Institution::create([
                'name' => 'Nombre viejo',
            ])->id;
        });

        $response = $this->actingAs($this->findAdminUser($tenant), 'web')
            ->put("http://{$domain}/tickets-valdi/admin/institutions/edit/{$institutionId}", [
                'name' => 'Nombre nuevo',
                'description' => 'Actualizada',
                'enabled' => '0',
            ]);

        $response->assertRedirect("http://{$domain}/tickets-valdi/admin/institutions/list");

        $tenant->run(function () use ($institutionId) {
            $this->assertDatabaseHas('institutions', [
                'id' => $institutionId,
                'name' => 'Nombre nuevo',
                'enabled' => false,
            ]);
        });
    }

    #[Test]
    public function nameMustBeUniqueOnCreate(): void
    {
        $domain = 'unique-name-institutions.localhost';
        $tenant = $this->createTenant('unique-name-institutions', 'tickets-valdi', $domain);

        $tenant->run(function () {
            $this->createAdminUser();
            Institution::create([
                'name' => 'Teatro Municipal',
            ]);
        });

        $response = $this->actingAs($this->findAdminUser($tenant), 'web')
            ->post("http://{$domain}/tickets-valdi/admin/institutions/create", [
                'name' => 'Teatro Municipal',
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function nameUniquenessIgnoresTheInstitutionBeingEdited(): void
    {
        $domain = 'unique-edit-institutions.localhost';
        $tenant = $this->createTenant('unique-edit-institutions', 'tickets-valdi', $domain);

        $institutionId = null;
        $tenant->run(function () use (&$institutionId) {
            $this->createAdminUser();
            $institutionId = Institution::create([
                'name' => 'Teatro Municipal',
            ])->id;
        });

        $response = $this->actingAs($this->findAdminUser($tenant), 'web')
            ->put("http://{$domain}/tickets-valdi/admin/institutions/edit/{$institutionId}", [
                'name' => 'Teatro Municipal',
                'enabled' => '1',
            ]);

        $response->assertSessionDoesntHaveErrors('name');
    }

    private function createAdminUser(): User
    {
        return User::create([
            'name' => 'Panel Admin',
            'email' => 'admin@tickets-valdi.test',
            'password' => Hash::make('secret123'),
            'enabled' => true,
        ]);
    }

    /**
     * actingAs() necesita la instancia de User resuelta con la conexión de BD
     * del tenant activa; por eso se busca de nuevo dentro de $tenant->run().
     */
    private function findAdminUser(\App\Models\Tenant $tenant): User
    {
        $user = null;
        $tenant->run(function () use (&$user) {
            $user = User::where('email', 'admin@tickets-valdi.test')->firstOrFail();
        });

        return $user;
    }
}
