<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Tests\Feature\TestDatabaseSeeders::class);
    }

    public function test_can_get_all_products()
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(5)
                 ->assertJsonStructure([
                    '*' => [
                        'id',
                        'name',
                        'sku',
                        'description',
                        'price',
                        'stock_quantity',
                        'category',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ]);
    }

    public function test_products_have_valid_price_format()
    {
        $response = $this->getJson('/api/products');

        $products = $response->json();

        foreach ($products as $product) {
            $this->assertIsNumeric($product['price']);
            $this->assertGreaterThanOrEqual(0, $product['price']);
            // Le prix peut être retourné comme string ou float selon la base de données
            $this->assertTrue(is_float($product['price']) || is_string($product['price']));
        }
    }

    public function test_products_have_valid_stock_quantity()
    {
        $response = $this->getJson('/api/products');

        $products = $response->json();

        foreach ($products as $product) {
            $this->assertIsNumeric($product['stock_quantity']);
            $this->assertGreaterThanOrEqual(0, $product['stock_quantity']);
            $this->assertIsInt($product['stock_quantity']);
        }
    }

    public function test_products_have_required_fields()
    {
        $response = $this->getJson('/api/products');

        $products = $response->json();

        foreach ($products as $product) {
            $this->assertArrayHasKey('name', $product);
            $this->assertArrayHasKey('sku', $product);
            $this->assertArrayHasKey('price', $product);
            $this->assertArrayHasKey('stock_quantity', $product);
            $this->assertNotEmpty($product['name']);
            $this->assertNotEmpty($product['sku']);
        }
    }

    public function test_products_have_valid_status()
    {
        $response = $this->getJson('/api/products');

        $products = $response->json();

        foreach ($products as $product) {
            $this->assertContains($product['status'], ['active', 'inactive']);
        }
    }

    public function test_products_have_unique_sku()
    {
        $response = $this->getJson('/api/products');

        $products = $response->json();
        $skus = array_column($products, 'sku');

        $this->assertEquals(count($skus), count(array_unique($skus)));
    }
}
