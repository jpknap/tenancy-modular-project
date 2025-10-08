<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LandlordAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        \Artisan::call('migrate');

        // Crear un usuario de prueba
        $this->user = User::create([
            'name' => 'Admin Landlord',
            'email' => 'admin@landlord.test',
            'password' => bcrypt('secret'),
        ]);
    }

    #[Test]
    public function itShowsLoginPage(): void
    {
        $response = $this->get('/landlord/login');
        $response->assertStatus(200)
            ->assertViewIs('landlord.auth.login');
    }

    #[Test]
    public function userCanLoginWithValidCredentials()
    {
        $response = $this->post('/landlord/login', [
            'email' => 'admin@landlord.test',
            'password' => 'secret',
        ]);

        $this->assertAuthenticated('web');
        $response->assertRedirect('/landlord/dashboard');
    }

    #[Test]
    public function userCannotLoginWithInvalidCredentials()
    {
        $response = $this->post('/landlord/login', [
            'email' => 'admin@landlord.test',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest('web');
        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function userCanLogout()
    {
        $response = $this->actingAs($this->user, 'web')
            ->post('/landlord/logout');

        $this->assertGuest('web');
        $response->assertRedirect('/landlord/login');
    }
}
