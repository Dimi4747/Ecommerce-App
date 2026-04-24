<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with(['customer', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $order = Order::create([
            'customer_id' => $validated['customer_id'],
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'status' => 'pending',
            'shipping_address' => $validated['shipping_address'] ?? null,
            'notes' => $validated['notes'] ?? null
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            $unitPrice = $product->price;
            $totalPrice = $unitPrice * $item['quantity'];
            $subtotal += $totalPrice;

            $order->orderItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice
            ]);
        }

        $taxAmount = $subtotal * 0.2; // 20% TVA
        $shippingAmount = 5.00;
        $totalAmount = $subtotal + $taxAmount + $shippingAmount;

        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'total_amount' => $totalAmount
        ]);

        return response()->json($order->load(['customer', 'orderItems.product']), 201);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json($order->load(['customer', 'orderItems.product']));
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string'
        ]);

        $order->update($validated);

        return response()->json($order->load(['customer', 'orderItems.product']));
    }

    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        return response()->json(null, 204);
    }

    public function statistics(): JsonResponse
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'completed_orders' => Order::where('status', 'delivered')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'total_customers' => \App\Models\Customer::count(),
            'total_products' => \App\Models\Product::count(),
            'recent_orders' => Order::with('customer')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'status_label' => $this->getStatusLabel($order->status),
                        'total_amount' => $order->total_amount,
                        'created_at' => $order->created_at,
                        'customer' => $order->customer
                    ];
                })
        ];

        return response()->json($stats);
    }

    private function getStatusLabel($status): string
    {
        return match($status) {
            'pending' => 'En attente',
            'processing' => 'En traitement',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => 'Inconnu'
        };
    }
}
