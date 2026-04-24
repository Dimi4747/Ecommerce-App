<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_json_response(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
                 ->assertJsonStructure(['status']);
    }

    public function test_authenticated_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'email' => $user->email,
                 ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_api_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->postJson('/api/example', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['field']);
    }
}
