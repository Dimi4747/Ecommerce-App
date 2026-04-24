<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'is_active' => true,
            ],
            [
                'name' => 'Clothing',
                'description' => 'Fashion and apparel',
                'is_active' => true,
            ],
            [
                'name' => 'Books',
                'description' => 'Books and educational materials',
                'is_active' => true,
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Home improvement and garden supplies',
                'is_active' => true,
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment and outdoor gear',
                'is_active' => true,
            ],
            [
                'name' => 'Toys & Games',
                'description' => 'Toys, games, and entertainment',
                'is_active' => true,
            ],
            [
                'name' => 'Health & Beauty',
                'description' => 'Health supplements and beauty products',
                'is_active' => true,
            ],
            [
                'name' => 'Automotive',
                'description' => 'Car parts and accessories',
                'is_active' => true,
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create subcategories for Electronics
        $electronics = Category::where('name', 'Electronics')->first();
        $subcategories = [
            ['name' => 'Smartphones', 'parent_id' => $electronics->id, 'is_active' => true],
            ['name' => 'Laptops', 'parent_id' => $electronics->id, 'is_active' => true],
            ['name' => 'Tablets', 'parent_id' => $electronics->id, 'is_active' => true],
            ['name' => 'Headphones', 'parent_id' => $electronics->id, 'is_active' => true],
            ['name' => 'Cameras', 'parent_id' => $electronics->id, 'is_active' => true],
        ];

        foreach ($subcategories as $subcategory) {
            Category::create($subcategory);
        }

        // Create subcategories for Clothing
        $clothing = Category::where('name', 'Clothing')->first();
        $clothingSubcategories = [
            ['name' => 'Men\'s Clothing', 'parent_id' => $clothing->id, 'is_active' => true],
            ['name' => 'Women\'s Clothing', 'parent_id' => $clothing->id, 'is_active' => true],
            ['name' => 'Kids\' Clothing', 'parent_id' => $clothing->id, 'is_active' => true],
            ['name' => 'Shoes', 'parent_id' => $clothing->id, 'is_active' => true],
            ['name' => 'Accessories', 'parent_id' => $clothing->id, 'is_active' => true],
        ];

        foreach ($clothingSubcategories as $subcategory) {
            Category::create($subcategory);
        }
    }
}
