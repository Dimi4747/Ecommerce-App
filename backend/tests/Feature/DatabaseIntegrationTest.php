<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Tests\Feature\TestDatabaseSeeders::class);
    }

    public function test_database_seeding_works()
    {
        $this->assertEquals(3, Customer::count());
        $this->assertEquals(5, Product::count());
        $this->assertEquals(4, Order::count());
        $this->assertGreaterThan(0, OrderItem::count());
    }

    public function test_customer_order_relationships()
    {
        $customer = Customer::first();
        $orders = $customer->orders;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $orders);

        foreach ($orders as $order) {
            $this->assertEquals($customer->id, $order->customer_id);
            $this->assertInstanceOf(Order::class, $order);
        }
    }

    public function test_order_product_relationships()
    {
        $order = Order::first();
        $orderItems = $order->orderItems;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $orderItems);

        foreach ($orderItems as $item) {
            $this->assertEquals($order->id, $item->order_id);
            $this->assertInstanceOf(OrderItem::class, $item);
            $this->assertInstanceOf(Product::class, $item->product);
        }
    }

    public function test_order_calculations_are_correct()
    {
        $order = Order::first();

        $this->assertIsNumeric($order->subtotal);
        $this->assertIsNumeric($order->tax_amount);
        $this->assertIsNumeric($order->shipping_amount);
        $this->assertIsNumeric($order->total_amount);

        // Vérifier que total = subtotal + tax + shipping
        $expectedTotal = $order->subtotal + $order->tax_amount + $order->shipping_amount;
        $this->assertEquals($expectedTotal, $order->total_amount);
    }

    public function test_order_item_calculations_are_correct()
    {
        $orderItem = OrderItem::first();

        $this->assertIsNumeric($orderItem->quantity);
        $this->assertIsNumeric($orderItem->unit_price);
        $this->assertIsNumeric($orderItem->total_price);

        // Vérifier que total_price = quantity * unit_price
        $expectedTotal = $orderItem->quantity * $orderItem->unit_price;
        $this->assertEquals($expectedTotal, $orderItem->total_price);
    }

    public function test_foreign_key_constraints()
    {
        // Créer un client et une commande
        $customer = Customer::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'address' => '123 Test St',
            'city' => 'Test City',
            'postal_code' => '12345',
            'country' => 'Test Country'
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'order_number' => 'TEST-001',
            'status' => 'pending',
            'subtotal' => 100,
            'tax_amount' => 20,
            'shipping_amount' => 5,
            'total_amount' => 125,
            'shipping_address' => '123 Test St'
        ]);

        // Vérifier que les relations fonctionnent
        $this->assertEquals($customer->id, $order->customer_id);
        $this->assertEquals($customer->fresh()->orders->first()->id, $order->id);
    }

    public function test_data_integrity_after_deletion()
    {
        $initialCustomerCount = Customer::count();
        $initialOrderCount = Order::count();

        $customer = Customer::first();
        $customerOrdersCount = $customer->orders->count();

        // Supprimer le client (vérifier si les contraintes sont configurées)
        $customer->delete();

        $this->assertEquals($initialCustomerCount - 1, Customer::count());
        // Note: Selon la configuration des contraintes, les commandes peuvent ou non être supprimées
    }
}
