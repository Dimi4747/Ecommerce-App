<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder($user, $shippingAddress, $billingAddress = null, $paymentMethod = null, $notes = null): Order
    {
        return DB::transaction(function () use ($user, $shippingAddress, $billingAddress, $paymentMethod, $notes) {
            $cart = Cart::where('user_id', $user->id)
                       ->where('status', 'active')
                       ->with('items.product')
                       ->firstOrFail();

            if ($cart->items->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            // Check stock availability
            foreach ($cart->items as $item) {
                if (!$item->isInStock()) {
                    throw new \Exception("Product '{$item->product->name}' is out of stock");
                }
            }

            $totalAmount = $cart->total_amount;
            $taxAmount = $totalAmount * 0.1; // 10% tax
            $shippingAmount = $this->calculateShipping($cart);
            $finalTotal = $totalAmount + $taxAmount + $shippingAmount;

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $finalTotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
                'notes' => $notes
            ]);

            // Create order items
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->quantity * $cartItem->price
                ]);

                // Update product stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Mark cart as converted
            $cart->update(['status' => 'converted']);

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \Exception('Invalid order status');
        }

        $order->update(['status' => $status]);

        // Handle status-specific logic
        switch ($status) {
            case 'cancelled':
                $this->handleOrderCancellation($order);
                break;
            case 'refunded':
                $this->handleOrderRefund($order);
                break;
        }

        return $order;
    }

    public function calculateShipping(Cart $cart): float
    {
        $totalWeight = $cart->items->sum(function ($item) {
            return $item->quantity * ($item->product->weight ?? 0);
        });

        // Simple shipping calculation: $5 base + $2 per kg
        return 5.0 + ($totalWeight * 2.0);
    }

    private function handleOrderCancellation(Order $order): void
    {
        // Restore stock
        foreach ($order->items as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }

        // Update payment status if needed
        if ($order->payment_status === 'paid') {
            $order->update(['payment_status' => 'refunded']);
        }
    }

    private function handleOrderRefund(Order $order): void
    {
        // Additional refund logic can be added here
        // For now, just update the payment status
        $order->update(['payment_status' => 'refunded']);
    }

    public function getOrderStats($userId = null): array
    {
        $query = Order::when($userId, function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        });

        return [
            'total_orders' => $query->count(),
            'total_revenue' => $query->where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => $query->where('status', 'pending')->count(),
            'completed_orders' => $query->where('status', 'delivered')->count(),
            'cancelled_orders' => $query->where('status', 'cancelled')->count(),
        ];
    }
}
