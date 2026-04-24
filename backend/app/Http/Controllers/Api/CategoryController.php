<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')
                            ->where('is_active', true)
                            ->ordered()
                            ->get();

        return response()->json($categories);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load(['products' => function($query) {
            $query->where('status', 'active')
                  ->orderBy('created_at', 'desc');
        }]);

        return response()->json($category);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image'] = $path;
        }

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer'
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $path = $request->file('image')->store('categories', 'public');
            $validated['image'] = $path;
        }

        $category->update($validated);

        return response()->json($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with associated products'
            ], 422);
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json(null, 204);
    }

    public function tree(): JsonResponse
    {
        $categories = Category::with(['children' => function($query) {
            $query->where('is_active', true)->withCount('products');
        }, 'children.children'])
                            ->whereNull('parent_id')
                            ->where('is_active', true)
                            ->withCount('products')
                            ->ordered()
                            ->get();

        return response()->json($categories);
    }
}
