<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_purchase_product(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/orders/purchase', [
                'product_id' => 1,
                'quantity' => 2,
                'price' => 29.99,
            ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Order placed successfully',
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'order' => [
                         'id',
                         'user_id',
                         'product_id',
                         'quantity',
                         'price',
                         'total',
                         'status',
                     ],
                 ]);

        $this->assertEquals(59.98, $response->json('order.total'));
    }

    public function test_unauthenticated_user_cannot_purchase(): void
    {
        $response = $this->postJson('/api/orders/purchase', [
            'product_id' => 1,
            'quantity' => 1,
            'price' => 29.99,
        ]);

        $response->assertStatus(401);
    }

    public function test_purchase_requires_product_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/orders/purchase', [
                'quantity' => 1,
                'price' => 29.99,
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['product_id']);
    }

    public function test_purchase_requires_quantity(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/orders/purchase', [
                'product_id' => 1,
                'price' => 29.99,
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['quantity']);
    }

    public function test_purchase_requires_price(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/orders/purchase', [
                'product_id' => 1,
                'quantity' => 1,
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['price']);
    }

    public function test_quantity_must_be_at_least_one(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/orders/purchase', [
                'product_id' => 1,
                'quantity' => 0,
                'price' => 29.99,
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['quantity']);
    }

    public function test_price_must_be_positive(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/orders/purchase', [
                'product_id' => 1,
                'quantity' => 1,
                'price' => -10,
            ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['price']);
    }

    public function test_order_total_is_calculated_correctly(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/orders/purchase', [
                'product_id' => 1,
                'quantity' => 3,
                'price' => 15.50,
            ]);

        $response->assertStatus(201);
        $this->assertEquals(46.50, $response->json('order.total'));
    }

    public function test_can_check_order_status(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/orders/1234/status');

        $response->assertStatus(200)
                 ->assertJson([
                     'order_id' => '1234',
                     'status' => 'completed',
                 ]);
    }
}
