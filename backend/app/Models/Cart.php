<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function addItem($productId, $quantity)
    {
        $product = Product::findOrFail($productId);
        
        if (!$product->isInStock()) {
            throw new \Exception('Product is not available or out of stock');
        }

        return $this->items()->updateOrCreate(
            ['product_id' => $productId],
            [
                'quantity' => $quantity,
                'price' => $product->base_price
            ]
        );
    }

    public function removeItem($productId)
    {
        return $this->items()->where('product_id', $productId)->delete();
    }

    public function updateItemQuantity($productId, $quantity)
    {
        $item = $this->items()->where('product_id', $productId)->first();
        
        if (!$item) {
            throw new \Exception('Item not found in cart');
        }

        if ($item->product->stock_quantity < $quantity) {
            throw new \Exception('Insufficient stock');
        }

        $item->update(['quantity' => $quantity]);
        
        return $item;
    }

    public function calculateTotal()
    {
        $total = $this->items()->sumRaw('quantity * price');
        $this->update(['total_amount' => $total]);
        
        return $total;
    }

    public function getItemCount()
    {
        return $this->items()->sum('quantity');
    }

    public function clear()
    {
        $this->items()->delete();
        $this->update(['total_amount' => 0]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
