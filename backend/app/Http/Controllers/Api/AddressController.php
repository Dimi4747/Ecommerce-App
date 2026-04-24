<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->orderBy('is_default', 'desc')->get();

        return response()->json($addresses);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:billing,shipping',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean'
        ]);

        $address = $request->user()->addresses()->create($validated);

        return response()->json($address, 201);
    }

    public function show(Address $address): JsonResponse
    {
        $this->authorize('view', $address);

        return response()->json($address);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        $this->authorize('update', $address);

        $validated = $request->validate([
            'type' => 'sometimes|in:billing,shipping',
            'address_line_1' => 'sometimes|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:100',
            'is_default' => 'boolean'
        ]);

        $address->update($validated);

        return response()->json($address);
    }

    public function destroy(Address $address): JsonResponse
    {
        $this->authorize('delete', $address);

        $address->delete();

        return response()->json(null, 204);
    }

    public function setDefault(Address $address): JsonResponse
    {
        $this->authorize('update', $address);

        $address->update(['is_default' => true]);

        return response()->json($address);
    }
}
