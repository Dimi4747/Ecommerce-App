<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Latest iPhone with advanced camera system and A17 Pro chip',
                'base_price' => 999.99,
                'sku' => 'IPHONE15PRO-128GB',
                'stock_quantity' => 50,
                'weight' => 0.187,
                'dimensions' => '146.6 x 70.6 x 8.25 mm',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Premium Android smartphone with S Pen and amazing camera',
                'base_price' => 1199.99,
                'sku' => 'GALAXYS24ULTRA-256GB',
                'stock_quantity' => 30,
                'weight' => 0.234,
                'dimensions' => '162.3 x 79.0 x 8.6 mm',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'MacBook Pro 14"',
                'description' => 'Powerful laptop with M3 Pro chip, perfect for professionals',
                'base_price' => 1999.99,
                'sku' => 'MACBOOKPRO14-M3PRO',
                'stock_quantity' => 25,
                'weight' => 1.55,
                'dimensions' => '311.5 x 220.5 x 15.5 mm',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Industry-leading noise canceling wireless headphones',
                'base_price' => 399.99,
                'sku' => 'SONY-WH1000XM5',
                'stock_quantity' => 40,
                'weight' => 0.250,
                'dimensions' => '254 x 201 x 73 mm',
                'status' => 'active',
                'is_featured' => false,
            ],
            [
                'name' => 'iPad Air',
                'description' => 'Versatile tablet perfect for work and play',
                'base_price' => 599.99,
                'sku' => 'IPADAIR-64GB',
                'stock_quantity' => 35,
                'weight' => 0.462,
                'dimensions' => '247.6 x 178.5 x 6.1 mm',
                'status' => 'active',
                'is_featured' => false,
            ],
            [
                'name' => 'Nike Air Max 90',
                'description' => 'Classic running shoes with iconic design',
                'base_price' => 120.00,
                'sku' => 'NIKE-AIRMAX90',
                'stock_quantity' => 100,
                'weight' => 0.320,
                'dimensions' => 'Varies by size',
                'status' => 'active',
                'is_featured' => false,
            ],
            [
                'name' => 'Levi\'s 501 Jeans',
                'description' => 'Original straight fit jeans, timeless style',
                'base_price' => 79.50,
                'sku' => 'LEVIS501-32W32L',
                'stock_quantity' => 80,
                'weight' => 0.500,
                'dimensions' => 'Varies by size',
                'status' => 'active',
                'is_featured' => false,
            ],
            [
                'name' => 'Canon EOS R6 Mark II',
                'description' => 'Professional mirrorless camera with 24MP sensor',
                'base_price' => 2499.99,
                'sku' => 'CANON-EOSR6M2',
                'stock_quantity' => 15,
                'weight' => 0.588,
                'dimensions' => '138.4 x 97.5 x 88.0 mm',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'PlayStation 5',
                'description' => 'Next-generation gaming console',
                'base_price' => 499.99,
                'sku' => 'PS5-CONSOLE',
                'stock_quantity' => 20,
                'weight' => 4.5,
                'dimensions' => '390 x 104 x 260 mm',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'Yoga Mat Premium',
                'description' => 'Non-slip exercise mat for yoga and fitness',
                'base_price' => 29.99,
                'sku' => 'YOGAMAT-PREMIUM',
                'stock_quantity' => 60,
                'weight' => 1.2,
                'dimensions' => '183 x 61 x 0.6 cm',
                'status' => 'active',
                'is_featured' => false,
            ]
        ];

        foreach ($products as $productData) {
            $category = $categories->random();
            
            // Assign appropriate category based on product type
            if (in_array($productData['name'], ['iPhone 15 Pro', 'Samsung Galaxy S24 Ultra', 'MacBook Pro 14"', 'iPad Air'])) {
                $category = Category::where('name', 'Smartphones')->orWhere('name', 'Laptops')->orWhere('name', 'Tablets')->first();
            } elseif (in_array($productData['name'], ['Sony WH-1000XM5 Headphones', 'Canon EOS R6 Mark II'])) {
                $category = Category::where('name', 'Headphones')->orWhere('name', 'Cameras')->first();
            } elseif (in_array($productData['name'], ['Nike Air Max 90', 'Levi\'s 501 Jeans'])) {
                $category = Category::where('name', 'Men\'s Clothing')->orWhere('name', 'Shoes')->first();
            } elseif ($productData['name'] === 'PlayStation 5') {
                $category = Category::where('name', 'Toys & Games')->first();
            } elseif ($productData['name'] === 'Yoga Mat Premium') {
                $category = Category::where('name', 'Sports & Outdoors')->first();
            }

            if ($category) {
                $productData['category_id'] = $category->id;
                Product::create($productData);
            }
        }

        // Create additional products for each category
        foreach ($categories->whereNull('parent_id') as $category) {
            for ($i = 0; $i < 5; $i++) {
                Product::create([
                    'name' => "Sample Product {$i} - {$category->name}",
                    'description' => "This is a sample product for the {$category->name} category. Great quality and value.",
                    'base_price' => rand(10, 500) + (rand(0, 99) / 100),
                    'category_id' => $category->id,
                    'sku' => strtoupper(Str::random(8)),
                    'stock_quantity' => rand(10, 100),
                    'weight' => rand(1, 50) / 10,
                    'dimensions' => rand(10, 100) . ' x ' . rand(10, 100) . ' x ' . rand(10, 100) . ' mm',
                    'status' => 'active',
                    'is_featured' => $i === 0,
                ]);
            }
        }
    }
}
