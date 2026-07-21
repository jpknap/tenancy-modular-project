<?php

namespace Tests\Feature\Api\ActivitiesBoard;

use App\Projects\ActivitiesBoard\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\CreatesTenants;
use Tests\TestCase;

class ActivityLogApiTest extends TestCase
{
    use CreatesTenants;

    protected function setUp(): void
    {
        parent::setUp();
        \Artisan::call('migrate:fresh');
    }

    #[Test]
    public function authenticatedUserCanCreateListUpdateAndDeleteLogsOfOwnActivity(): void
    {
        $domain = 'owner-logs.localhost';
        $tenant = $this->createTenant('owner-logs', 'activities-board', $domain);

        $activityId = null;
        $tenant->run(function () use (&$activityId) {
            $owner = User::create([
                'name' => 'Owner',
                'email' => 'owner@logs.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            $activityId = $owner->activityItems()
                ->create([
                    'name' => 'Correr',
                ])->id;
        });

        $token = $this->loginAndGetToken($domain, 'owner@logs.test');
        $headers = [
            'Authorization' => "Bearer {$token}",
        ];

        $create = $this->withHeaders($headers)
            ->postJson("http://{$domain}/activities-board/api/activities/{$activityId}/logs", [
                'client_uuid' => Str::uuid()->toString(),
                'occurred_at' => now()
                    ->toIso8601String(),
                'note' => '30 minutos',
            ]);

        $create->assertStatus(201)
            ->assertJsonFragment([
                'note' => '30 minutos',
            ]);

        $logId = $create->json('id');

        $index = $this->withHeaders($headers)
            ->getJson("http://{$domain}/activities-board/api/activities/{$activityId}/logs")
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'current_page', 'per_page', 'total']);

        $this->assertCount(1, $index->json('data'));

        $this->withHeaders($headers)
            ->patchJson("http://{$domain}/activities-board/api/activities/{$activityId}/logs/{$logId}", [
                'note' => 'actualizado',
            ])
            ->assertStatus(200)
            ->assertJsonFragment([
                'note' => 'actualizado',
            ]);

        $this->withHeaders($headers)
            ->deleteJson("http://{$domain}/activities-board/api/activities/{$activityId}/logs/{$logId}")
            ->assertStatus(204);

        $this->assertDatabaseCount('activity_logs', 0);
    }

    #[Test]
    public function creatingLogWithSameClientUuidTwiceIsIdempotent(): void
    {
        $domain = 'idempotent-logs.localhost';
        $tenant = $this->createTenant('idempotent-logs', 'activities-board', $domain);

        $activityId = null;
        $tenant->run(function () use (&$activityId) {
            $owner = User::create([
                'name' => 'Owner',
                'email' => 'owner@idempotent.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            $activityId = $owner->activityItems()
                ->create([
                    'name' => 'Correr',
                ])->id;
        });

        $token = $this->loginAndGetToken($domain, 'owner@idempotent.test');
        $headers = [
            'Authorization' => "Bearer {$token}",
        ];
        $clientUuid = Str::uuid()->toString();

        $payload = [
            'client_uuid' => $clientUuid,
            'occurred_at' => now()
                ->toIso8601String(),
        ];

        $first = $this->withHeaders($headers)
            ->postJson("http://{$domain}/activities-board/api/activities/{$activityId}/logs", $payload);
        $first->assertStatus(201);

        $second = $this->withHeaders($headers)
            ->postJson("http://{$domain}/activities-board/api/activities/{$activityId}/logs", $payload);
        $second->assertStatus(200)
            ->assertJsonFragment([
                'id' => $first->json('id'),
            ]);

        $this->assertDatabaseCount('activity_logs', 1);
    }

    #[Test]
    public function userCannotCreateOrListLogsOnAnotherUsersActivity(): void
    {
        $domain = 'intruder-logs.localhost';
        $tenant = $this->createTenant('intruder-logs', 'activities-board', $domain);

        $activityId = null;
        $tenant->run(function () use (&$activityId) {
            $owner = User::create([
                'name' => 'Owner',
                'email' => 'owner@intruderlogs.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            User::create([
                'name' => 'Intruder',
                'email' => 'intruder@intruderlogs.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            $activityId = $owner->activityItems()
                ->create([
                    'name' => 'Correr',
                ])->id;
        });

        $token = $this->loginAndGetToken($domain, 'intruder@intruderlogs.test');
        $headers = [
            'Authorization' => "Bearer {$token}",
        ];

        $this->withHeaders($headers)
            ->getJson("http://{$domain}/activities-board/api/activities/{$activityId}/logs")
            ->assertStatus(404);

        $this->withHeaders($headers)
            ->postJson("http://{$domain}/activities-board/api/activities/{$activityId}/logs", [
                'client_uuid' => Str::uuid()->toString(),
                'occurred_at' => now()
                    ->toIso8601String(),
            ])
            ->assertStatus(404);
    }

    #[Test]
    public function unauthenticatedRequestsToLogsAreRejected(): void
    {
        $domain = 'anon-logs.localhost';
        $tenant = $this->createTenant('anon-logs', 'activities-board', $domain);

        $activityId = null;
        $tenant->run(function () use (&$activityId) {
            $owner = User::create([
                'name' => 'Owner',
                'email' => 'owner@anonlogs.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            $activityId = $owner->activityItems()
                ->create([
                    'name' => 'Correr',
                ])->id;
        });

        $this->getJson("http://{$domain}/activities-board/api/activities/{$activityId}/logs")
            ->assertStatus(401);
    }

    #[Test]
    public function creatingLogValidatesRequiredFields(): void
    {
        $domain = 'invalid-logs.localhost';
        $tenant = $this->createTenant('invalid-logs', 'activities-board', $domain);

        $activityId = null;
        $tenant->run(function () use (&$activityId) {
            $owner = User::create([
                'name' => 'Owner',
                'email' => 'owner@invalidlogs.test',
                'password' => Hash::make('secret123'),
                'enabled' => true,
            ]);
            $activityId = $owner->activityItems()
                ->create([
                    'name' => 'Correr',
                ])->id;
        });

        $token = $this->loginAndGetToken($domain, 'owner@invalidlogs.test');

        $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])
            ->postJson("http://{$domain}/activities-board/api/activities/{$activityId}/logs", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['client_uuid', 'occurred_at']);
    }

    private function loginAndGetToken(string $domain, string $email, string $password = 'secret123'): string
    {
        return (string) $this->postJson("http://{$domain}/activities-board/api/auth/login", [
            'email' => $email,
            'password' => $password,
        ])->json('token');
    }
}
