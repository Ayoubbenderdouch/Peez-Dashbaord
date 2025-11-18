<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ShopResource;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Get all shops with optional filters
     */
    public function index(Request $request)
    {
        $query = Shop::with(['neighborhood', 'category', 'ratings']);

        // Filter by neighborhood
        if ($request->has('neighborhood_id')) {
            $query->where('neighborhood_id', $request->neighborhood_id);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $shops = $query->paginate(20);

        return ShopResource::collection($shops);
    }

    /**
     * Get single shop
     */
    public function show($id)
    {
        $shop = Shop::with(['neighborhood', 'category', 'ratings.user'])
            ->findOrFail($id);

        return new ShopResource($shop);
    }

    /**
     * Get nearby shops based on coordinates
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'sometimes|numeric|min:1|max:50', // km
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;
        $radius = $request->radius ?? 5; // Default 5km

        // Haversine formula for distance calculation
        $shops = Shop::with(['neighborhood', 'category', 'ratings'])
            ->selectRaw("
                *,
                (6371 * acos(cos(radians(?)) 
                * cos(radians(latitude)) 
                * cos(radians(longitude) - radians(?)) 
                + sin(radians(?)) 
                * sin(radians(latitude)))) AS distance
            ", [$lat, $lng, $lat])
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->paginate(20);

        return ShopResource::collection($shops);
    }

    /**
     * Get shops by neighborhood
     */
    public function byNeighborhood($neighborhoodId)
    {
        $shops = Shop::with(['category', 'ratings'])
            ->where('neighborhood_id', $neighborhoodId)
            ->paginate(20);

        return ShopResource::collection($shops);
    }

    /**
     * Get shops by category
     */
    public function byCategory($categoryId)
    {
        $shops = Shop::with(['neighborhood', 'ratings'])
            ->where('category_id', $categoryId)
            ->paginate(20);

        return ShopResource::collection($shops);
    }
}
