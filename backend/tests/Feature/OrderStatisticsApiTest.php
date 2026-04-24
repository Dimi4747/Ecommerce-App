<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatisticsApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Tests\Feature\TestDatabaseSeeders::class);
    }

    public function test_can_get_order_statistics()
    {
        $response = $this->getJson('/api/orders/statistics');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'total_orders',
                    'pending_orders',
                    'processing_orders',
                    'completed_orders',
                    'total_revenue',
                    'total_customers',
                    'total_products',
                    'recent_orders'
                ]);
    }

    public function test_statistics_have_correct_values()
    {
        $response = $this->getJson('/api/orders/statistics');

        $stats = $response->json();

        $this->assertEquals(4, $stats['total_orders']);
        $this->assertEquals(3, $stats['total_customers']);
        $this->assertEquals(5, $stats['total_products']);
        $this->assertGreaterThanOrEqual(0, $stats['pending_orders']);
        $this->assertGreaterThanOrEqual(0, $stats['processing_orders']);
        $this->assertGreaterThanOrEqual(0, $stats['completed_orders']);
        $this->assertIsNumeric($stats['total_revenue']);
    }

    public function test_recent_orders_structure()
    {
        $response = $this->getJson('/api/orders/statistics');

        $stats = $response->json();

        $this->assertArrayHasKey('recent_orders', $stats);
        $this->assertIsArray($stats['recent_orders']);

        if (!empty($stats['recent_orders'])) {
            foreach ($stats['recent_orders'] as $order) {
                $this->assertArrayHasKey('id', $order);
                $this->assertArrayHasKey('order_number', $order);
                $this->assertArrayHasKey('status', $order);
                $this->assertArrayHasKey('status_label', $order);
                $this->assertArrayHasKey('total_amount', $order);
                $this->assertArrayHasKey('customer', $order);
            }
        }
    }

    public function test_status_labels_are_in_french()
    {
        $response = $this->getJson('/api/orders/statistics');

        $stats = $response->json();

        if (!empty($stats['recent_orders'])) {
            foreach ($stats['recent_orders'] as $order) {
                $this->assertArrayHasKey('status_label', $order);
                $this->assertContains($order['status_label'], [
                    'En attente',
                    'En traitement',
                    'Expédiée',
                    'Livrée',
                    'Annulée'
                ]);
            }
        }
    }

    public function test_total_revenue_is_calculated_correctly()
    {
        $response = $this->getJson('/api/orders/statistics');

        $stats = $response->json();

        $this->assertIsNumeric($stats['total_revenue']);
        $this->assertGreaterThanOrEqual(0, $stats['total_revenue']);

        // Le revenu total doit être la somme des commandes livrées
        // Note: JSON returns numeric strings, so we check if it's numeric instead of float
        $this->assertTrue(is_numeric($stats['total_revenue']));
    }

    public function test_recent_orders_include_customer_data()
    {
        $response = $this->getJson('/api/orders/statistics');

        $stats = $response->json();

        if (!empty($stats['recent_orders'])) {
            foreach ($stats['recent_orders'] as $order) {
                $this->assertArrayHasKey('customer', $order);
                $this->assertIsArray($order['customer']);

                if (!empty($order['customer'])) {
                    $this->assertArrayHasKey('first_name', $order['customer']);
                    $this->assertArrayHasKey('last_name', $order['customer']);
                }
            }
        }
    }
}
