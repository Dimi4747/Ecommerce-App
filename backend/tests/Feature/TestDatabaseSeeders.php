<?php

namespace Tests\Feature;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class TestDatabaseSeeders extends Seeder
{
    public function run(): void
    {
        // Créer les clients d'abord
        $customers = [
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean.dupont@email.com',
                'phone' => '0123456789',
                'address' => '123 Rue de la République',
                'city' => 'Paris',
                'postal_code' => '75001',
                'country' => 'France'
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Martin',
                'email' => 'marie.martin@email.com',
                'phone' => '0234567890',
                'address' => '456 Avenue des Champs-Élysées',
                'city' => 'Paris',
                'postal_code' => '75008',
                'country' => 'France'
            ],
            [
                'first_name' => 'Pierre',
                'last_name' => 'Durand',
                'email' => 'pierre.durand@email.com',
                'phone' => '0345678901',
                'address' => '789 Boulevard Saint-Germain',
                'city' => 'Lyon',
                'postal_code' => '69001',
                'country' => 'France'
            ]
        ];

        $createdCustomers = [];
        foreach ($customers as $customerData) {
            $customer = Customer::create($customerData);
            $createdCustomers[] = $customer;
        }

        // Créer les produits ensuite
        $products = [
            [
                'name' => 'Laptop Pro 15"',
                'sku' => 'LP15-001',
                'description' => 'Ordinateur portable haute performance avec écran 15 pouces',
                'price' => 1299.99,
                'stock_quantity' => 25,
                'is_active' => true
            ],
            [
                'name' => 'Smartphone X',
                'sku' => 'SMX-002',
                'description' => 'Smartphone dernière génération avec double caméra',
                'price' => 899.99,
                'stock_quantity' => 50,
                'is_active' => true
            ],
            [
                'name' => 'Tablet Pro 12"',
                'sku' => 'TP12-003',
                'description' => 'Tablette professionnelle avec stylet inclus',
                'price' => 649.99,
                'stock_quantity' => 30,
                'is_active' => true
            ],
            [
                'name' => 'Casque Audio Sans Fil',
                'sku' => 'CAS-004',
                'description' => 'Casque Bluetooth avec réduction de bruit',
                'price' => 199.99,
                'stock_quantity' => 100,
                'is_active' => true
            ],
            [
                'name' => 'Clavier Mécanique RGB',
                'sku' => 'CLR-005',
                'description' => 'Clavier gaming avec rétroéclairage RGB',
                'price' => 149.99,
                'stock_quantity' => 40,
                'is_active' => true
            ]
        ];

        $createdProducts = [];
        foreach ($products as $productData) {
            $product = Product::create($productData);
            $createdProducts[] = $product;
        }

        // Créer les commandes en dernier
        $orders = [
            [
                'customer_id' => $createdCustomers[0]->id,
                'order_number' => 'ORD-2024001',
                'status' => 'pending',
                'subtotal' => 1299.99,
                'tax_amount' => 260.00,
                'shipping_amount' => 5.00,
                'total_amount' => 1564.99,
                'shipping_address' => '123 Rue de la République, 75001 Paris',
                'notes' => 'Livraison après 18h',
                'items' => [
                    ['product_id' => $createdProducts[0]->id, 'quantity' => 1, 'unit_price' => 1299.99, 'total_price' => 1299.99]
                ]
            ],
            [
                'customer_id' => $createdCustomers[1]->id,
                'order_number' => 'ORD-2024002',
                'status' => 'processing',
                'subtotal' => 1549.98,
                'tax_amount' => 310.00,
                'shipping_amount' => 5.00,
                'total_amount' => 1864.98,
                'shipping_address' => '456 Avenue des Champs-Élysées, 75008 Paris',
                'notes' => '',
                'items' => [
                    ['product_id' => $createdProducts[1]->id, 'quantity' => 1, 'unit_price' => 899.99, 'total_price' => 899.99],
                    ['product_id' => $createdProducts[3]->id, 'quantity' => 1, 'unit_price' => 199.99, 'total_price' => 199.99],
                    ['product_id' => $createdProducts[4]->id, 'quantity' => 2, 'unit_price' => 149.99, 'total_price' => 299.99]
                ]
            ],
            [
                'customer_id' => $createdCustomers[2]->id,
                'order_number' => 'ORD-2024003',
                'status' => 'delivered',
                'subtotal' => 649.99,
                'tax_amount' => 130.00,
                'shipping_amount' => 5.00,
                'total_amount' => 784.99,
                'shipping_address' => '789 Boulevard Saint-Germain, 69001 Lyon',
                'notes' => '',
                'items' => [
                    ['product_id' => $createdProducts[2]->id, 'quantity' => 1, 'unit_price' => 649.99, 'total_price' => 649.99]
                ]
            ],
            [
                'customer_id' => $createdCustomers[0]->id,
                'order_number' => 'ORD-2024004',
                'status' => 'shipped',
                'subtotal' => 199.99,
                'tax_amount' => 40.00,
                'shipping_amount' => 5.00,
                'total_amount' => 244.99,
                'shipping_address' => '123 Rue de la République, 75001 Paris',
                'notes' => '',
                'items' => [
                    ['product_id' => $createdProducts[3]->id, 'quantity' => 1, 'unit_price' => 199.99, 'total_price' => 199.99]
                ]
            ]
        ];

        foreach ($orders as $orderData) {
            $items = $orderData['items'];
            unset($orderData['items']);
            
            $order = Order::create($orderData);
            
            foreach ($items as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['total_price']
                ]);
            }
        }
    }
}
