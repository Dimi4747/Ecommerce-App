<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Tests\Feature\TestDatabaseSeeders::class);
    }

    public function test_can_get_all_customers()
    {
        $response = $this->getJson('/api/customers');

        $response->assertStatus(200)
                 ->assertJsonCount(3)
                 ->assertJsonStructure([
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'phone',
                        'address',
                        'city',
                        'postal_code',
                        'country',
                        'created_at',
                        'total_orders',
                        'total_spent'
                    ]
                ]);
    }

    public function test_customer_data_includes_order_statistics()
    {
        $response = $this->getJson('/api/customers');

        $customers = $response->json();

        // Vérifier que le premier client a des statistiques de commandes
        $this->assertArrayHasKey('total_orders', $customers[0]);
        $this->assertArrayHasKey('total_spent', $customers[0]);
        $this->assertIsNumeric($customers[0]['total_orders']);
        $this->assertIsNumeric($customers[0]['total_spent']);
    }

    public function test_customers_have_valid_email_format()
    {
        $response = $this->getJson('/api/customers');

        $customers = $response->json();

        foreach ($customers as $customer) {
            $this->assertMatchesRegularExpression('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $customer['email']);
        }
    }

    public function test_customers_have_required_fields()
    {
        $response = $this->getJson('/api/customers');

        $customers = $response->json();

        foreach ($customers as $customer) {
            $this->assertArrayHasKey('first_name', $customer);
            $this->assertArrayHasKey('last_name', $customer);
            $this->assertArrayHasKey('email', $customer);
            $this->assertNotEmpty($customer['first_name']);
            $this->assertNotEmpty($customer['last_name']);
            $this->assertNotEmpty($customer['email']);
        }
    }
}
