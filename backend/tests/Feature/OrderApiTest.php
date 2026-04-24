<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Tests\Feature\TestDatabaseSeeders::class);
    }

    public function test_can_get_all_orders()
    {
        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'data',
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                    'links'
                ])
                 ->assertJsonCount(4, 'data');
    }

    public function test_orders_have_valid_structure()
    {
        $response = $this->getJson('/api/orders');

        $orders = $response->json('data');

        foreach ($orders as $order) {
            $this->assertArrayHasKey('id', $order);
            $this->assertArrayHasKey('customer_id', $order);
            $this->assertArrayHasKey('order_number', $order);
            $this->assertArrayHasKey('status', $order);
            $this->assertArrayHasKey('total_amount', $order);
            $this->assertArrayHasKey('customer', $order);
            $this->assertArrayHasKey('order_items', $order);
        }
    }

    public function test_orders_have_valid_total_amount()
    {
        $response = $this->getJson('/api/orders');

        $orders = $response->json('data');

        foreach ($orders as $order) {
            $this->assertIsNumeric($order['total_amount']);
            $this->assertGreaterThanOrEqual(0, $order['total_amount']);
        }
    }

    public function test_orders_have_valid_status()
    {
        $response = $this->getJson('/api/orders');

        $orders = $response->json('data');

        foreach ($orders as $order) {
            $this->assertContains($order['status'], ['pending', 'processing', 'shipped', 'delivered', 'cancelled']);
        }
    }

    public function test_can_get_single_order()
    {
        $order = Order::first();

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'id',
                    'customer_id',
                    'order_number',
                    'status',
                    'subtotal',
                    'tax_amount',
                    'shipping_amount',
                    'total_amount',
                    'shipping_address',
                    'notes',
                    'created_at',
                    'updated_at',
                    'customer',
                    'order_items'
                ]);
    }

    public function test_single_order_includes_customer_data()
    {
        $order = Order::first();

        $response = $this->getJson("/api/orders/{$order->id}");

        $orderData = $response->json();

        $this->assertArrayHasKey('customer', $orderData);
        $this->assertArrayHasKey('first_name', $orderData['customer']);
        $this->assertArrayHasKey('email', $orderData['customer']);
    }

    public function test_single_order_includes_order_items()
    {
        $order = Order::first();

        $response = $this->getJson("/api/orders/{$order->id}");

        $orderData = $response->json();

        $this->assertArrayHasKey('order_items', $orderData);
        $this->assertIsArray($orderData['order_items']);

        if (!empty($orderData['order_items'])) {
            foreach ($orderData['order_items'] as $item) {
                $this->assertArrayHasKey('product_id', $item);
                $this->assertArrayHasKey('quantity', $item);
                $this->assertArrayHasKey('unit_price', $item);
                $this->assertArrayHasKey('total_price', $item);
            }
        }
    }
}
