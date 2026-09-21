<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\CreatesTenants;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    // Sin RefreshDatabase: sus transacciones no conviven con los reconnects
    // de tenancy sobre sqlite de archivo (ver SanctumSetupTest). Se usa
    // migrate:fresh explícito.
    use CreatesTenants;

    protected function setUp(): void
    {
        parent::setUp();
        \Artisan::call('migrate:fresh');
    }

    #[Test]
    public function loginWithValidCredentialsReturnsTokenAndAuthenticatesSubsequentRequest(): void
    {
        User::create([
            'name' => 'Landlord Admin',
            'email' => 'admin@landlord.test',
            'password' => Hash::make('secret123'),
            'enabled' => true,
        ]);

        $response = $this->postJson('/landlord/api/auth/login', [
            'email' => 'admin@landlord.test',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'email'],
            ]);

        $token = $response->json('token');

        $me = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/landlord/api/auth/me');

        $me->assertStatus(200)
            ->assertJsonFragment([
                'email' => 'admin@landlord.test',
            ]);
    }

    #[Test]
    public function loginWithInvalidCredentialsReturns401WithoutCreatingToken(): void
    {
        User::create([
            'name' => 'Landlord Admin',
            'email' => 'admin@landlord.test',
            'password' => Hash::make('secret123'),
            'enabled' => true,
        ]);

        $response = $this->postJson('/landlord/api/auth/login', [
            'email' => 'admin@landlord.test',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function loginWithDisabledUserReturns403WithoutCreatingToken(): void
    {
        User::create([
            'name' => 'Disabled Admin',
            'email' => 'disabled@landlord.test',
            'password' => Hash::make('secret123'),
            'enabled' => false,
        ]);

        $response = $this->postJson('/landlord/api/auth/login', [
            'email' => 'disabled@landlord.test',
            'password' => 'secret123',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function logoutRevokesTokenSoSubsequentRequestIsUnauthorized(): void
    {
        User::create([
            'name' => 'Landlord Admin',
            'email' => 'admin@landlord.test',
            'password' => Hash::make('secret123'),
            'enabled' => true,
        ]);

        $token = $this->postJson('/landlord/api/auth/login', [
            'email' => 'admin@landlord.test',
            'password' => 'secret123',
        ])->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/landlord/api/auth/logout')
            ->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);

        // El guard 'sanctum' (RequestGuard) cachea el usuario resuelto en la
        // primera llamada dentro de un mismo test y no se resetea solo entre
        // requests simulados (setRequest() no limpia $this->user). Sin este
        // forgetGuards(), el request siguiente reusaría al usuario ya
        // autenticado en vez de volver a validar el token contra la DB.
        Auth::forgetGuards();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/landlord/api/auth/me')
            ->assertStatus(401);
    }

    #[Test]
    public function meEndpointOnActivitiesBoardTenantReturnsUserAndRejectsMissingToken(): void
    {
        $tenant = $this->createTenant('acme', 'activities-board', 'acme.localhost');

        $tenant->run(function () {
            User::create([
                'name' => 'Tenant User',
                'email' => 'user@acme.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
        });

        $token = $this->postJson('http://acme.localhost/activities-board/api/auth/login', [
            'email' => 'user@acme.test',
            'password' => 'secret123',
        ])->json('token');

        $this->assertNotEmpty($token);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('http://acme.localhost/activities-board/api/auth/me')
            ->assertStatus(200)
            ->assertJsonFragment([
                'email' => 'user@acme.test',
            ]);

        // Ver comentario equivalente en el test de logout sobre forgetGuards().
        // Además, withHeader() deja el header seteado para todos los
        // requests siguientes del test (no es "de una sola vez"), así que
        // hay que sacarlo explícitamente para simular un request sin token.
        Auth::forgetGuards();

        $this->withoutHeader('Authorization')
            ->getJson('http://acme.localhost/activities-board/api/auth/me')
            ->assertStatus(401);
    }

    #[Test]
    public function loginExceedingRateLimitReturns429(): void
    {
        User::create([
            'name' => 'Landlord Admin',
            'email' => 'throttled@landlord.test',
            'password' => Hash::make('secret123'),
            'enabled' => true,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/landlord/api/auth/login', [
                'email' => 'throttled@landlord.test',
                'password' => 'wrong-password',
            ])->assertStatus(401);
        }

        $this->postJson('/landlord/api/auth/login', [
            'email' => 'throttled@landlord.test',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }

    #[Test]
    public function loginResponseHasNoSetCookieHeaderAndDoesNotStartSession(): void
    {
        User::create([
            'name' => 'Landlord Admin',
            'email' => 'admin@landlord.test',
            'password' => Hash::make('secret123'),
            'enabled' => true,
        ]);

        $response = $this->postJson('/landlord/api/auth/login', [
            'email' => 'admin@landlord.test',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertHeaderMissing('Set-Cookie');

        $this->assertFalse(session()->isStarted());
    }
}
