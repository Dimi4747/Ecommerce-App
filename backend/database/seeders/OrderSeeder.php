<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [
            [
                'customer_id' => 1,
                'order_number' => 'ORD-2024001',
                'status' => 'pending',
                'subtotal' => 1299.99,
                'tax_amount' => 260.00,
                'shipping_amount' => 5.00,
                'total_amount' => 1564.99,
                'shipping_address' => '123 Rue de la République, 75001 Paris',
                'notes' => 'Livraison après 18h',
                'items' => [
                    ['product_id' => 1, 'quantity' => 1, 'unit_price' => 1299.99, 'total_price' => 1299.99]
                ]
            ],
            [
                'customer_id' => 2,
                'order_number' => 'ORD-2024002',
                'status' => 'processing',
                'subtotal' => 1549.98,
                'tax_amount' => 310.00,
                'shipping_amount' => 5.00,
                'total_amount' => 1864.98,
                'shipping_address' => '456 Avenue des Champs-Élysées, 75008 Paris',
                'items' => [
                    ['product_id' => 2, 'quantity' => 1, 'unit_price' => 899.99, 'total_price' => 899.99],
                    ['product_id' => 4, 'quantity' => 1, 'unit_price' => 199.99, 'total_price' => 199.99],
                    ['product_id' => 5, 'quantity' => 2, 'unit_price' => 149.99, 'total_price' => 299.99],
                    ['product_id' => 3, 'quantity' => 1, 'unit_price' => 149.99, 'total_price' => 149.99]
                ]
            ],
            [
                'customer_id' => 3,
                'order_number' => 'ORD-2024003',
                'status' => 'delivered',
                'subtotal' => 649.99,
                'tax_amount' => 130.00,
                'shipping_amount' => 5.00,
                'total_amount' => 784.99,
                'shipping_address' => '789 Boulevard Saint-Germain, 69001 Lyon',
                'items' => [
                    ['product_id' => 3, 'quantity' => 1, 'unit_price' => 649.99, 'total_price' => 649.99]
                ]
            ],
            [
                'customer_id' => 1,
                'order_number' => 'ORD-2024004',
                'status' => 'shipped',
                'subtotal' => 199.99,
                'tax_amount' => 40.00,
                'shipping_amount' => 5.00,
                'total_amount' => 244.99,
                'shipping_address' => '123 Rue de la République, 75001 Paris',
                'items' => [
                    ['product_id' => 4, 'quantity' => 1, 'unit_price' => 199.99, 'total_price' => 199.99]
                ]
            ]
        ];

        foreach ($orders as $orderData) {
            $items = $orderData['items'];
            unset($orderData['items']);

            $order = Order::create($orderData);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price']
                ]);
            }
        }
    }
}
