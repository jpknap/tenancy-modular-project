<?php

namespace Tests\Feature\Api\ActivitiesBoard;

use App\Projects\ActivitiesBoard\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\CreatesTenants;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use CreatesTenants;

    protected function setUp(): void
    {
        parent::setUp();
        \Artisan::call('migrate:fresh');
    }

    #[Test]
    public function authenticatedUserCanCreateListShowUpdateAndDeleteOwnCategory(): void
    {
        $domain = 'owner-categories.localhost';
        $tenant = $this->createTenant('owner-categories', 'activities-board', $domain);

        $tenant->run(function () {
            User::create([
                'name' => 'Owner',
                'email' => 'owner@categories.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
        });

        $token = $this->loginAndGetToken($domain, 'owner@categories.test');
        $headers = [
            'Authorization' => "Bearer {$token}",
        ];

        $create = $this->withHeaders($headers)
            ->postJson("http://{$domain}/activities-board/api/categories", [
                'name' => 'Salud',
                'color' => '#00ff00',
            ]);

        $create->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Salud',
                'color' => '#00ff00',
            ]);

        $id = $create->json('id');

        $this->withHeaders($headers)
            ->getJson("http://{$domain}/activities-board/api/categories")
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $id,
            ]);

        $this->withHeaders($headers)
            ->getJson("http://{$domain}/activities-board/api/categories/{$id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Salud',
            ]);

        $this->withHeaders($headers)
            ->patchJson("http://{$domain}/activities-board/api/categories/{$id}", [
                'color' => '#0000ff',
            ])
            ->assertStatus(200)
            ->assertJsonFragment([
                'color' => '#0000ff',
                'name' => 'Salud',
            ]);

        $this->withHeaders($headers)
            ->deleteJson("http://{$domain}/activities-board/api/categories/{$id}")
            ->assertStatus(204);

        $this->assertDatabaseCount('categories', 0);
    }

    #[Test]
    public function userCannotViewUpdateOrDeleteAnotherUsersCategory(): void
    {
        $domain = 'intruder-categories.localhost';
        $tenant = $this->createTenant('intruder-categories', 'activities-board', $domain);

        $categoryId = null;
        $tenant->run(function () use (&$categoryId) {
            $owner = User::create([
                'name' => 'Owner',
                'email' => 'owner@intrudercat.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            User::create([
                'name' => 'Intruder',
                'email' => 'intruder@intrudercat.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            $categoryId = $owner->categoryItems()
                ->create([
                    'name' => 'Trabajo',
                ])->id;
        });

        $token = $this->loginAndGetToken($domain, 'intruder@intrudercat.test');
        $headers = [
            'Authorization' => "Bearer {$token}",
        ];

        $this->withHeaders($headers)
            ->getJson("http://{$domain}/activities-board/api/categories/{$categoryId}")
            ->assertStatus(404);

        $this->withHeaders($headers)
            ->patchJson("http://{$domain}/activities-board/api/categories/{$categoryId}", [
                'name' => 'Hack',
            ])
            ->assertStatus(404);

        $this->withHeaders($headers)
            ->deleteJson("http://{$domain}/activities-board/api/categories/{$categoryId}")
            ->assertStatus(404);

        $this->assertDatabaseHas('categories', [
            'id' => $categoryId,
            'name' => 'Trabajo',
        ]);
    }

    #[Test]
    public function unauthenticatedRequestsToCategoriesAreRejected(): void
    {
        $domain = 'anon-categories.localhost';
        $this->createTenant('anon-categories', 'activities-board', $domain);

        $this->getJson("http://{$domain}/activities-board/api/categories")
            ->assertStatus(401);
        $this->postJson("http://{$domain}/activities-board/api/categories", [
            'name' => 'x',
        ])->assertStatus(401);
    }

    #[Test]
    public function creatingCategoryValidatesRequiredFields(): void
    {
        $domain = 'invalid-categories.localhost';
        $tenant = $this->createTenant('invalid-categories', 'activities-board', $domain);

        $tenant->run(function () {
            User::create([
                'name' => 'Owner',
                'email' => 'owner@invalidcat.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
        });

        $token = $this->loginAndGetToken($domain, 'owner@invalidcat.test');

        $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])
            ->postJson("http://{$domain}/activities-board/api/categories", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    private function loginAndGetToken(string $domain, string $email, string $password = 'secret123'): string
    {
        return (string) $this->postJson("http://{$domain}/activities-board/api/auth/login", [
            'email' => $email,
            'password' => $password,
        ])->json('token');
    }
}
