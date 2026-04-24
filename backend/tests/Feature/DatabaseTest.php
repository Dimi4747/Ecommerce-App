<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_connection_works(): void
    {
        $this->assertDatabaseCount('users', 0);

        User::factory()->create();

        $this->assertDatabaseCount('users', 1);
    }

    public function test_user_can_be_stored_in_database(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_can_be_updated_in_database(): void
    {
        $user = User::factory()->create();

        $user->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_user_can_be_deleted_from_database(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', ['id' => $user->id]);

        $user->delete();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_factory_creates_valid_user(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
        $this->assertTrue(strlen($user->password) > 0);
    }
}
