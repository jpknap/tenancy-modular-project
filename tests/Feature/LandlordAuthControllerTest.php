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

        $this->user = User::create([
            'name' => 'Admin Landlord',
            'email' => 'admin@landlord.test',
            'password' => bcrypt('secret'),
        ]);
    }

    #[Test]
    public function itShowsLoginPage(): void
    {
        $response = $this->get('/landlord/auth/login');
        $response->assertStatus(200)
            ->assertViewIs('landlord.auth.login');
    }

    #[Test]
    public function userCanLoginWithValidCredentials()
    {
        $response = $this->post('/landlord/auth/login', [
            'email' => 'admin@landlord.test',
            'password' => 'secret',
        ]);

        $this->assertAuthenticated('landlord');
        $response->assertRedirect(route('landlord.admin.tenants.list'));
    }

    #[Test]
    public function userCannotLoginWithInvalidCredentials()
    {
        $response = $this->post('/landlord/auth/login', [
            'email' => 'admin@landlord.test',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest('landlord');
        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function userCanLogout()
    {
        $response = $this->actingAs($this->user, 'landlord')
            ->post('/landlord/auth/logout');

        $this->assertGuest('landlord');
        $response->assertRedirect(route('landlord.auth.login'));
    }
}
