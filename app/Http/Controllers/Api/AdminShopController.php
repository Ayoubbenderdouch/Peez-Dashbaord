<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminShopController extends Controller
{
    /**
     * Get All Shops (with pagination and filtering)
     */
    public function index(Request $request)
    {
        $query = Shop::with(['neighborhood', 'category', 'vendor']);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by neighborhood
        if ($request->has('neighborhood_id')) {
            $query->where('neighborhood_id', $request->neighborhood_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $perPage = $request->get('per_page', 15);
        $shops = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $shops->map(function ($shop) {
                return [
                    'id' => $shop->id,
                    'name' => $shop->name,
                    'address' => $shop->address,
                    'phone' => $shop->phone,
                    'discountPercent' => $shop->discount_percent,
                    'isActive' => (bool) $shop->is_active,
                    'lat' => $shop->lat,
                    'lng' => $shop->lng,
                    'neighborhood' => [
                        'id' => $shop->neighborhood->id,
                        'name' => $shop->neighborhood->name_fr,
                        'nameAr' => $shop->neighborhood->name_ar,
                    ],
                    'category' => [
                        'id' => $shop->category->id,
                        'name' => $shop->category->name,
                        'nameFr' => $shop->category->name_fr,
                    ],
                    'vendor' => $shop->vendor ? [
                        'id' => $shop->vendor->id,
                        'name' => $shop->vendor->name,
                        'email' => $shop->vendor->email,
                    ] : null,
                    'createdAt' => $shop->created_at->toIso8601String(),
                ];
            }),
            'meta' => [
                'current_page' => $shops->currentPage(),
                'last_page' => $shops->lastPage(),
                'per_page' => $shops->perPage(),
                'total' => $shops->total(),
            ],
        ]);
    }

    /**
     * Get Single Shop Details
     */
    public function show($id)
    {
        $shop = Shop::with(['neighborhood', 'category', 'vendor'])->find($id);

        if (!$shop) {
            return response()->json([
                'message' => 'Shop not found',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => $shop->id,
                'name' => $shop->name,
                'address' => $shop->address,
                'phone' => $shop->phone,
                'discountPercent' => $shop->discount_percent,
                'isActive' => (bool) $shop->is_active,
                'lat' => $shop->lat,
                'lng' => $shop->lng,
                'neighborhoodId' => $shop->neighborhood_id,
                'categoryId' => $shop->category_id,
                'vendorId' => $shop->vendor_id,
                'neighborhood' => [
                    'id' => $shop->neighborhood->id,
                    'name' => $shop->neighborhood->name_fr,
                    'nameAr' => $shop->neighborhood->name_ar,
                ],
                'category' => [
                    'id' => $shop->category->id,
                    'name' => $shop->category->name,
                    'nameFr' => $shop->category->name_fr,
                ],
                'vendor' => $shop->vendor ? [
                    'id' => $shop->vendor->id,
                    'name' => $shop->vendor->name,
                    'email' => $shop->vendor->email,
                ] : null,
                'createdAt' => $shop->created_at->toIso8601String(),
                'updatedAt' => $shop->updated_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Create New Shop
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'category_id' => 'required|exists:categories,id',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'is_active' => 'boolean',
            'vendor_id' => 'nullable|exists:users,id',
        ]);

        // Check if neighborhood+category combination already exists
        $exists = Shop::where('neighborhood_id', $validated['neighborhood_id'])
            ->where('category_id', $validated['category_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A shop with this category already exists in this neighborhood',
                'errors' => [
                    'category_id' => ['This category is already taken in this neighborhood'],
                ],
            ], 422);
        }

        $shop = Shop::create([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'neighborhood_id' => $validated['neighborhood_id'],
            'category_id' => $validated['category_id'],
            'discount_percent' => $validated['discount_percent'],
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'is_active' => $validated['is_active'] ?? true,
            'vendor_id' => $validated['vendor_id'] ?? null,
        ]);

        $shop->load(['neighborhood', 'category', 'vendor']);

        return response()->json([
            'message' => 'Shop created successfully',
            'data' => [
                'id' => $shop->id,
                'name' => $shop->name,
                'address' => $shop->address,
                'phone' => $shop->phone,
                'discountPercent' => $shop->discount_percent,
                'isActive' => (bool) $shop->is_active,
                'lat' => $shop->lat,
                'lng' => $shop->lng,
                'neighborhood' => [
                    'id' => $shop->neighborhood->id,
                    'name' => $shop->neighborhood->name_fr,
                ],
                'category' => [
                    'id' => $shop->category->id,
                    'name' => $shop->category->name,
                ],
            ],
        ], 201);
    }

    /**
     * Update Shop
     */
    public function update(Request $request, $id)
    {
        $shop = Shop::find($id);

        if (!$shop) {
            return response()->json([
                'message' => 'Shop not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|min:3|max:100',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'neighborhood_id' => 'sometimes|exists:neighborhoods,id',
            'category_id' => 'sometimes|exists:categories,id',
            'discount_percent' => 'sometimes|numeric|min:0|max:100',
            'lat' => 'sometimes|numeric|between:-90,90',
            'lng' => 'sometimes|numeric|between:-180,180',
            'is_active' => 'boolean',
            'vendor_id' => 'nullable|exists:users,id',
        ]);

        // Check uniqueness if neighborhood or category changed
        if (isset($validated['neighborhood_id']) || isset($validated['category_id'])) {
            $neighborhoodId = $validated['neighborhood_id'] ?? $shop->neighborhood_id;
            $categoryId = $validated['category_id'] ?? $shop->category_id;

            $exists = Shop::where('neighborhood_id', $neighborhoodId)
                ->where('category_id', $categoryId)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'A shop with this category already exists in this neighborhood',
                    'errors' => [
                        'category_id' => ['This category is already taken in this neighborhood'],
                    ],
                ], 422);
            }
        }

        $shop->update($validated);
        $shop->load(['neighborhood', 'category', 'vendor']);

        return response()->json([
            'message' => 'Shop updated successfully',
            'data' => [
                'id' => $shop->id,
                'name' => $shop->name,
                'address' => $shop->address,
                'phone' => $shop->phone,
                'discountPercent' => $shop->discount_percent,
                'isActive' => (bool) $shop->is_active,
                'lat' => $shop->lat,
                'lng' => $shop->lng,
            ],
        ]);
    }

    /**
     * Delete Shop
     */
    public function destroy($id)
    {
        $shop = Shop::find($id);

        if (!$shop) {
            return response()->json([
                'message' => 'Shop not found',
            ], 404);
        }

        $shopName = $shop->name;
        $shop->delete();

        return response()->json([
            'message' => "Shop '{$shopName}' deleted successfully",
        ]);
    }

    /**
     * Get Available Vendors (users with vendor role without a shop)
     */
    public function availableVendors()
    {
        $vendors = User::where('role', 'vendor')
            ->whereDoesntHave('shop')
            ->select('id', 'name', 'email', 'phone')
            ->get();

        return response()->json([
            'data' => $vendors,
        ]);
    }
}
