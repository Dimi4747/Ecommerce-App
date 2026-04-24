<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reviews = Review::with(['user', 'product'])
                        ->where('is_approved', true)
                        ->when($request->product_id, function($query, $productId) {
                            $query->where('product_id', $productId);
                        })
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return response()->json($reviews);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $user = $request->user();

        // Check if user has purchased the product
        $hasPurchased = Order::where('user_id', $user->id)
                            ->where('payment_status', 'paid')
                            ->whereHas('items', function($query) use ($validated) {
                                $query->where('product_id', $validated['product_id']);
                            })
                            ->exists();

        if (!$hasPurchased) {
            return response()->json([
                'message' => 'You can only review products you have purchased'
            ], 422);
        }

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', $user->id)
                               ->where('product_id', $validated['product_id'])
                               ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this product'
            ], 422);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $validated['product_id'],
            'order_id' => $validated['order_id'] ?? null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_approved' => true // Auto-approve for now, can be changed to require admin approval
        ]);

        $review->load(['user', 'product']);

        return response()->json($review, 201);
    }

    public function update(Request $request, Review $review): JsonResponse
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $review->update($validated);

        $review->load(['user', 'product']);

        return response()->json($review);
    }

    public function destroy(Review $review): JsonResponse
    {
        $this->authorize('delete', $review);

        $review->delete();

        return response()->json(null, 204);
    }

    public function productReviews(Product $product): JsonResponse
    {
        $reviews = $product->reviews()
                          ->where('is_approved', true)
                          ->with('user')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        $stats = [
            'average_rating' => $product->reviews()->where('is_approved', true)->avg('rating') ?? 0,
            'total_reviews' => $product->reviews()->where('is_approved', true)->count(),
            'rating_distribution' => $product->reviews()
                                            ->where('is_approved', true)
                                            ->selectRaw('rating, count(*) as count')
                                            ->groupBy('rating')
                                            ->pluck('count', 'rating')
                                            ->toArray()
        ];

        return response()->json([
            'reviews' => $reviews,
            'stats' => $stats
        ]);
    }

    public function userReviews(Request $request): JsonResponse
    {
        $reviews = $request->user()->reviews()
                                  ->with('product')
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(10);

        return response()->json($reviews);
    }
}
