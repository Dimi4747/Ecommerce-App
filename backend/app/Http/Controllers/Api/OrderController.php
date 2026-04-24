<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
                               ->with(['items.product', 'payments'])
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);

        return response()->json($orders);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);
        
        $order->load(['items.product', 'payments', 'user']);

        return response()->json($order);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_address' => 'required|array',
            'billing_address' => 'nullable|array',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        try {
            $order = $this->orderService->createOrder(
                $request->user(),
                $validated['shipping_address'],
                $validated['billing_address'] ?? null,
                $validated['payment_method'],
                $validated['notes'] ?? null
            );

            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string'
        ]);

        $order->update($validated);

        return response()->json($order);
    }

    public function cancel(Order $order): JsonResponse
    {
        $this->authorize('cancel', $order);

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Order cannot be cancelled'
            ], 422);
        }

        $order->update(['status' => 'cancelled']);

        // Restore stock
        foreach ($order->items as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }

        return response()->json($order);
    }

    public function track(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at
        ]);
    }
}
