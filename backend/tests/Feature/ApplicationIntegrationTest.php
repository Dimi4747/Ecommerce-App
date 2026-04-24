<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Tests\Feature\TestDatabaseSeeders::class);
    }

    public function test_complete_api_workflow()
    {
        // 1. Tester la récupération des clients
        $customersResponse = $this->getJson('/api/customers');
        $customersResponse->assertStatus(200);
        $customers = $customersResponse->json();
        $this->assertNotEmpty($customers);

        // 2. Tester la récupération des produits
        $productsResponse = $this->getJson('/api/products');
        $productsResponse->assertStatus(200);
        $products = $productsResponse->json();
        $this->assertNotEmpty($products);

        // 3. Tester la récupération des commandes
        $ordersResponse = $this->getJson('/api/orders');
        $ordersResponse->assertStatus(200);
        $orders = $ordersResponse->json('data');
        $this->assertNotEmpty($orders);

        // 4. Tester les statistiques
        $statsResponse = $this->getJson('/api/orders/statistics');
        $statsResponse->assertStatus(200);
        $stats = $statsResponse->json();

        // 5. Vérifier la cohérence des données
        $this->assertEquals(count($customers), $stats['total_customers']);
        $this->assertEquals(count($products), $stats['total_products']);
        $this->assertEquals(count($orders), $stats['total_orders']);
    }

    public function test_dashboard_data_flow()
    {
        // Simuler le flux de données que le dashboard frontend utiliserait

        // 1. Obtenir les statistiques principales
        $statsResponse = $this->getJson('/api/orders/statistics');
        $stats = $statsResponse->json();

        // 2. Obtenir les données détaillées pour chaque section
        $customersResponse = $this->getJson('/api/customers');
        $productsResponse = $this->getJson('/api/products');
        $ordersResponse = $this->getJson('/api/orders');

        // 3. Vérifier que toutes les données nécessaires sont présentes
        $this->assertArrayHasKey('total_orders', $stats);
        $this->assertArrayHasKey('pending_orders', $stats);
        $this->assertArrayHasKey('processing_orders', $stats);
        $this->assertArrayHasKey('completed_orders', $stats);
        $this->assertArrayHasKey('total_revenue', $stats);
        $this->assertArrayHasKey('total_customers', $stats);
        $this->assertArrayHasKey('total_products', $stats);
        $this->assertArrayHasKey('recent_orders', $stats);

        // 4. Vérifier que les données sont cohérentes
        $this->assertTrue(count($stats['recent_orders']) > 0);
        $this->assertIsNumeric($stats['total_revenue']);
        $this->assertGreaterThanOrEqual(0, $stats['total_revenue']);
    }

    public function test_order_creation_and_retrieval_workflow()
    {
        // Créer un nouveau client
        $customerData = [
            'first_name' => 'Jean',
            'last_name' => 'Test',
            'email' => 'jean.test@example.com',
            'phone' => '0123456789',
            'address' => '123 Rue Test',
            'city' => 'Paris',
            'postal_code' => '75001',
            'country' => 'France'
        ];

        $customer = Customer::create($customerData);
        $this->assertInstanceOf(Customer::class, $customer);

        // Créer une nouvelle commande
        $orderData = [
            'customer_id' => $customer->id,
            'order_number' => 'TEST-001',
            'status' => 'pending',
            'subtotal' => 100,
            'tax_amount' => 20,
            'shipping_amount' => 5,
            'total_amount' => 125,
            'shipping_address' => '123 Rue Test, 75001 Paris'
        ];

        $order = Order::create($orderData);
        $this->assertInstanceOf(Order::class, $order);

        // Ajouter des items à la commande
        $product = Product::first();
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->price,
            'total_price' => $product->price * 2
        ]);

        // Vérifier que la commande peut être récupérée via l'API
        $response = $this->getJson("/api/orders/{$order->id}");
        $response->assertStatus(200);

        $orderData = $response->json();
        $this->assertEquals($customer->id, $orderData['customer_id']);
        $this->assertEquals('TEST-001', $orderData['order_number']);
        $this->assertArrayHasKey('customer', $orderData);
        $this->assertArrayHasKey('order_items', $orderData);
        $this->assertCount(1, $orderData['order_items']);
    }

    public function test_error_handling_and_edge_cases()
    {
        // Tester une commande qui n'existe pas
        $response = $this->getJson('/api/orders/999999');
        $response->assertStatus(404);

        // Tester des données invalides
        $response = $this->postJson('/api/orders', []);
        $response->assertStatus(422); // Validation error

        // Tester les endpoints avec des données vides (si applicable)
        // Note: Ceci dépend de l'implémentation exacte de vos endpoints
    }

    public function test_performance_and_response_times()
    {
        $startTime = microtime(true);

        // Tester plusieurs requêtes consécutives
        $this->getJson('/api/customers');
        $this->getJson('/api/products');
        $this->getJson('/api/orders');
        $this->getJson('/api/orders/statistics');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Les requêtes devraient prendre moins de 2 secondes
        $this->assertLessThan(2.0, $executionTime);
    }

    public function test_data_consistency_across_endpoints()
    {
        // Obtenir les données de différentes sources
        $statsResponse = $this->getJson('/api/orders/statistics');
        $ordersResponse = $this->getJson('/api/orders');
        $customersResponse = $this->getJson('/api/customers');

        $stats = $statsResponse->json();
        $orders = $ordersResponse->json('data');
        $customers = $customersResponse->json();

        // Vérifier la cohérence des données
        $this->assertEquals(count($orders), $stats['total_orders']);
        $this->assertEquals(count($customers), $stats['total_customers']);

        // Vérifier que les statuts sont cohérents
        $orderStatuses = array_column($orders, 'status');
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        foreach ($orderStatuses as $status) {
            $this->assertContains($status, $validStatuses);
        }
    }
}
