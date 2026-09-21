<?php

namespace Tests\Feature\Api\ActivitiesBoard;

use App\Projects\ActivitiesBoard\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\CreatesTenants;
use Tests\TestCase;

class ActivityApiTest extends TestCase
{
    // Sin RefreshDatabase: ver comentario equivalente en ApiAuthTest.
    use CreatesTenants;

    protected function setUp(): void
    {
        parent::setUp();
        \Artisan::call('migrate:fresh');
    }

    #[Test]
    public function authenticatedUserCanCreateListShowUpdateAndDeleteOwnActivity(): void
    {
        $domain = 'owner-activities.localhost';
        $tenant = $this->createTenant('owner-activities', 'activities-board', $domain);

        $tenant->run(function () {
            User::create([
                'name' => 'Owner',
                'email' => 'owner@acme.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
        });

        $token = $this->loginAndGetToken($domain, 'owner@acme.test');
        $headers = [
            'Authorization' => "Bearer {$token}",
        ];

        $create = $this->withHeaders($headers)
            ->postJson("http://{$domain}/activities-board/api/activities", [
                'name' => 'Leer',
                'description' => 'Leer 20 minutos',
                'color' => '#ff0000',
            ]);

        $create->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Leer',
                'color' => '#ff0000',
            ]);

        $id = $create->json('id');

        $this->withHeaders($headers)
            ->getJson("http://{$domain}/activities-board/api/activities")
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $id,
            ]);

        $this->withHeaders($headers)
            ->getJson("http://{$domain}/activities-board/api/activities/{$id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Leer',
            ]);

        $this->withHeaders($headers)
            ->patchJson("http://{$domain}/activities-board/api/activities/{$id}", [
                'color' => '#00ff00',
            ])
            ->assertStatus(200)
            ->assertJsonFragment([
                'color' => '#00ff00',
                'name' => 'Leer',
            ]);

        $this->withHeaders($headers)
            ->deleteJson("http://{$domain}/activities-board/api/activities/{$id}")
            ->assertStatus(204);

        $this->assertDatabaseCount('activities', 0);
    }

    #[Test]
    public function userCannotViewUpdateOrDeleteAnotherUsersActivity(): void
    {
        $domain = 'intruder-activities.localhost';
        $tenant = $this->createTenant('intruder-activities', 'activities-board', $domain);

        $activityId = null;
        $tenant->run(function () use (&$activityId) {
            $owner = User::create([
                'name' => 'Owner',
                'email' => 'owner@intruder.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            User::create([
                'name' => 'Intruder',
                'email' => 'intruder@intruder.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            $activityId = $owner->activityItems()
                ->create([
                    'name' => 'Correr',
                ])->id;
        });

        $token = $this->loginAndGetToken($domain, 'intruder@intruder.test');
        $headers = [
            'Authorization' => "Bearer {$token}",
        ];

        $this->withHeaders($headers)
            ->getJson("http://{$domain}/activities-board/api/activities/{$activityId}")
            ->assertStatus(404);

        $this->withHeaders($headers)
            ->patchJson("http://{$domain}/activities-board/api/activities/{$activityId}", [
                'name' => 'Hack',
            ])
            ->assertStatus(404);

        $this->withHeaders($headers)
            ->deleteJson("http://{$domain}/activities-board/api/activities/{$activityId}")
            ->assertStatus(404);

        $this->assertDatabaseHas('activities', [
            'id' => $activityId,
            'name' => 'Correr',
        ]);
    }

    #[Test]
    public function unauthenticatedRequestsToActivitiesAreRejected(): void
    {
        $domain = 'anon-activities.localhost';
        $this->createTenant('anon-activities', 'activities-board', $domain);

        $this->getJson("http://{$domain}/activities-board/api/activities")
            ->assertStatus(401);
        $this->postJson("http://{$domain}/activities-board/api/activities", [
            'name' => 'x',
        ])->assertStatus(401);
    }

    #[Test]
    public function creatingActivityValidatesRequiredFields(): void
    {
        $domain = 'invalid-activities.localhost';
        $tenant = $this->createTenant('invalid-activities', 'activities-board', $domain);

        $tenant->run(function () {
            User::create([
                'name' => 'Owner',
                'email' => 'owner@invalid.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
        });

        $token = $this->loginAndGetToken($domain, 'owner@invalid.test');

        $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])
            ->postJson("http://{$domain}/activities-board/api/activities", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function creatingAndUpdatingActivityAssignsOwnCategories(): void
    {
        $domain = 'assign-categories.localhost';
        $tenant = $this->createTenant('assign-categories', 'activities-board', $domain);

        $categoryIds = [];
        $tenant->run(function () use (&$categoryIds) {
            $owner = User::create([
                'name' => 'Owner',
                'email' => 'owner@assigncat.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            $categoryIds[] = $owner->categoryItems()
                ->create([
                    'name' => 'Salud',
                ])->id;
            $categoryIds[] = $owner->categoryItems()
                ->create([
                    'name' => 'Trabajo',
                ])->id;
        });

        $token = $this->loginAndGetToken($domain, 'owner@assigncat.test');
        $headers = [
            'Authorization' => "Bearer {$token}",
        ];

        $create = $this->withHeaders($headers)
            ->postJson("http://{$domain}/activities-board/api/activities", [
                'name' => 'Leer',
                'category_ids' => $categoryIds,
            ]);

        $create->assertStatus(201)
            ->assertJsonCount(2, 'categories');

        $id = $create->json('id');

        $this->withHeaders($headers)
            ->patchJson("http://{$domain}/activities-board/api/activities/{$id}", [
                'category_ids' => [$categoryIds[0]],
            ])
            ->assertStatus(200)
            ->assertJsonCount(1, 'categories')
            ->assertJsonFragment([
                'id' => $categoryIds[0],
            ]);
    }

    #[Test]
    public function assigningAnotherUsersCategoryToActivityIsRejected(): void
    {
        $domain = 'assign-intruder-categories.localhost';
        $tenant = $this->createTenant('assign-intruder-categories', 'activities-board', $domain);

        $foreignCategoryId = null;
        $tenant->run(function () use (&$foreignCategoryId) {
            User::create([
                'name' => 'Owner',
                'email' => 'owner@assignintrudercat.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            $other = User::create([
                'name' => 'Other',
                'email' => 'other@assignintrudercat.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            $foreignCategoryId = $other->categoryItems()
                ->create([
                    'name' => 'Ajena',
                ])->id;
        });

        $token = $this->loginAndGetToken($domain, 'owner@assignintrudercat.test');

        $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])
            ->postJson("http://{$domain}/activities-board/api/activities", [
                'name' => 'Leer',
                'category_ids' => [$foreignCategoryId],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['category_ids.0']);
    }

    private function loginAndGetToken(string $domain, string $email, string $password = 'secret123'): string
    {
        return (string) $this->postJson("http://{$domain}/activities-board/api/auth/login", [
            'email' => $email,
            'password' => $password,
        ])->json('token');
    }
}
