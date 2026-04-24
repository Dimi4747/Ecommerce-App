<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request->user());
        $cart->load(['items.product', 'items.product.category']);

        return response()->json($cart);
    }

    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($validated['product_id']);
        
        if (!$product->isInStock() || $product->stock_quantity < $validated['quantity']) {
            return response()->json([
                'message' => 'Product is not available or insufficient stock'
            ], 422);
        }

        $cart = $this->getOrCreateCart($request->user());
        
        $cartItem = $cart->items()->updateOrCreate(
            ['product_id' => $validated['product_id']],
            [
                'quantity' => $validated['quantity'],
                'price' => $product->base_price
            ]
        );

        $this->updateCartTotal($cart);
        
        $cartItem->load('product');

        return response()->json($cartItem);
    }

    public function updateItem(Request $request, CartItem $cartItem): JsonResponse
    {
        $this->authorize('update', $cartItem);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $product = $cartItem->product;
        
        if ($product->stock_quantity < $validated['quantity']) {
            return response()->json([
                'message' => 'Insufficient stock'
            ], 422);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);
        
        $this->updateCartTotal($cartItem->cart);
        
        $cartItem->load('product');

        return response()->json($cartItem);
    }

    public function removeItem(CartItem $cartItem): JsonResponse
    {
        $this->authorize('delete', $cartItem);
        
        $cart = $cartItem->cart;
        $cartItem->delete();
        
        $this->updateCartTotal($cart);

        return response()->json(null, 204);
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request->user());
        $cart->items()->delete();
        $cart->update(['total_amount' => 0]);

        return response()->json(null, 204);
    }

    public function count(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request->user());
        $count = $cart->items()->sum('quantity');

        return response()->json(['count' => $count]);
    }

    private function getOrCreateCart($user): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['total_amount' => 0]
        );
    }

    private function updateCartTotal(Cart $cart): void
    {
        $total = $cart->items()->sumRaw('quantity * price');
        $cart->update(['total_amount' => $total]);
    }
}
