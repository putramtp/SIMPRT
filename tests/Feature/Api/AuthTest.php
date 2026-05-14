<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);
        $user->assignRole('teknisi');

        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'roles']]);
    }

    public function test_wrong_credentials_returns_401(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'wrongpassword',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_get_me(): void
    {
        $user = User::factory()->create();
        $user->assignRole('sales');

        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
             ->getJson('/api/auth/me')
             ->assertOk()
             ->assertJsonFragment(['email' => $user->email]);
    }

    public function test_unauthenticated_request_to_me_returns_401(): void
    {
        $this->getJson('/api/auth/me')->assertUnauthorized();
    }

    public function test_user_can_logout(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
             ->postJson('/api/auth/logout')
             ->assertOk()
             ->assertJsonFragment(['message' => 'Logout berhasil.']);

        // Verify token was actually deleted from DB
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_teknisi_can_access_tasks_api(): void
    {
        $user = User::factory()->create();
        $user->assignRole('teknisi');
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
             ->getJson('/api/tasks')
             ->assertOk()
             ->assertJsonStructure(['data', 'current_page', 'total']);
    }

    public function test_teknisi_cannot_submit_report_via_api_without_token(): void
    {
        $this->postJson('/api/reports', [
            'task_id'     => 1,
            'description' => 'Test',
        ])->assertUnauthorized();
    }
}
