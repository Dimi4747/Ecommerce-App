<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateCart($user): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['total_amount' => 0]
        );
    }

    public function addItem(Cart $cart, int $productId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($cart, $productId, $quantity) {
            $product = Product::findOrFail($productId);

            if (!$product->isInStock() || $product->stock_quantity < $quantity) {
                throw new \Exception('Product is not available or insufficient stock');
            }

            $cartItem = $cart->items()->updateOrCreate(
                ['product_id' => $productId],
                [
                    'quantity' => DB::raw("quantity + {$quantity}"),
                    'price' => $product->base_price
                ]
            );

            // Refresh to get actual values
            $cartItem->refresh();

            // Ensure quantity doesn't exceed stock
            if ($cartItem->quantity > $product->stock_quantity) {
                $cartItem->update(['quantity' => $product->stock_quantity]);
            }

            $this->recalculateTotal($cart);

            return $cartItem->refresh();
        });
    }

    public function updateItemQuantity(Cart $cart, int $productId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($cart, $productId, $quantity) {
            $cartItem = $cart->items()->where('product_id', $productId)->firstOrFail();

            $product = $cartItem->product;

            if ($product->stock_quantity < $quantity) {
                throw new \Exception('Insufficient stock');
            }

            $cartItem->update([
                'quantity' => $quantity,
                'price' => $product->base_price
            ]);

            $this->recalculateTotal($cart);

            return $cartItem->refresh();
        });
    }

    public function removeItem(Cart $cart, int $productId): void
    {
        DB::transaction(function () use ($cart, $productId) {
            $cart->items()->where('product_id', $productId)->delete();
            $this->recalculateTotal($cart);
        });
    }

    public function clearCart(Cart $cart): void
    {
        DB::transaction(function () use ($cart) {
            $cart->items()->delete();
            $cart->update(['total_amount' => 0]);
        });
    }

    public function recalculateTotal(Cart $cart): float
    {
        $total = $cart->items()->sumRaw('quantity * price');
        $cart->update(['total_amount' => $total]);

        return $total;
    }

    public function mergeCarts($user, ?string $guestCartId = null): Cart
    {
        $userCart = $this->getOrCreateCart($user);

        if ($guestCartId) {
            $guestCart = Cart::where('id', $guestCartId)
                            ->where('status', 'active')
                            ->first();

            if ($guestCart) {
                foreach ($guestCart->items as $guestItem) {
                    $existingItem = $userCart->items()
                                            ->where('product_id', $guestItem->product_id)
                                            ->first();

                    if ($existingItem) {
                        $existingItem->update([
                            'quantity' => $existingItem->quantity + $guestItem->quantity
                        ]);
                    } else {
                        $userCart->items()->create([
                            'product_id' => $guestItem->product_id,
                            'quantity' => $guestItem->quantity,
                            'price' => $guestItem->price
                        ]);
                    }
                }

                $guestCart->items()->delete();
                $guestCart->delete();
            }
        }

        $this->recalculateTotal($userCart);

        return $userCart->refresh();
    }

    public function validateCartItems(Cart $cart): array
    {
        $issues = [];

        foreach ($cart->items as $item) {
            $product = $item->product;

            if ($product->status !== 'active') {
                $issues[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'issue' => 'Product is no longer available'
                ];
            } elseif ($product->stock_quantity < $item->quantity) {
                $issues[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'issue' => 'Insufficient stock',
                    'available' => $product->stock_quantity,
                    'requested' => $item->quantity
                ];
            } elseif ($product->base_price != $item->price) {
                $issues[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'issue' => 'Price has changed',
                    'old_price' => $item->price,
                    'new_price' => $product->base_price
                ];
            }
        }

        return $issues;
    }
}
