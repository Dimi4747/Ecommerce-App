<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Process a purchase order
     */
    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        // Simulate order processing
        $order = [
            'id' => rand(1000, 9999),
            'user_id' => Auth::id(),
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'total' => $validated['quantity'] * $validated['price'],
            'status' => 'pending',
            'created_at' => now(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'order' => $order,
        ], 201);
    }

    /**
     * Get order status
     */
    public function status($orderId)
    {
        // Simulate order status check
        return response()->json([
            'order_id' => $orderId,
            'status' => 'completed',
            'message' => 'Order has been processed',
        ]);
    }
}
