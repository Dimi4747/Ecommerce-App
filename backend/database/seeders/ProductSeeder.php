<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Pro 15"',
                'description' => 'Ordinateur portable haute performance avec écran 15 pouces',
                'price' => 1299.99,
                'stock_quantity' => 25,
                'sku' => 'LP15-001',
                'image_url' => 'https://via.placeholder.com/300x300/3b82f6/ffffff?text=Laptop',
                'is_active' => true
            ],
            [
                'name' => 'Smartphone X',
                'description' => 'Smartphone dernière génération avec double caméra',
                'price' => 899.99,
                'stock_quantity' => 50,
                'sku' => 'SMX-002',
                'image_url' => 'https://via.placeholder.com/300x300/10b981/ffffff?text=Smartphone',
                'is_active' => true
            ],
            [
                'name' => 'Tablet Pro 12"',
                'description' => 'Tablette professionnelle avec stylet inclus',
                'price' => 649.99,
                'stock_quantity' => 30,
                'sku' => 'TP12-003',
                'image_url' => 'https://via.placeholder.com/300x300/f59e0b/ffffff?text=Tablet',
                'is_active' => true
            ],
            [
                'name' => 'Casque Audio Sans Fil',
                'description' => 'Casque Bluetooth avec réduction de bruit',
                'price' => 199.99,
                'stock_quantity' => 100,
                'sku' => 'CAS-004',
                'image_url' => 'https://via.placeholder.com/300x300/ef4444/ffffff?text=Casque',
                'is_active' => true
            ],
            [
                'name' => 'Clavier Mécanique RGB',
                'description' => 'Clavier gaming avec rétroéclairage RGB',
                'price' => 149.99,
                'stock_quantity' => 40,
                'sku' => 'CLR-005',
                'image_url' => 'https://via.placeholder.com/300x300/8b5cf6/ffffff?text=Clavier',
                'is_active' => true
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
