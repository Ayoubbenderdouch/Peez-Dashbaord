<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\RatingResource;
use App\Models\Rating;
use App\Models\Shop;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Rate a shop
     */
    public function rate(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'stars' => 'required|integer|min:1|max:5',
        ]);

        $shop = Shop::findOrFail($validated['shop_id']);

        // Check if user already rated this shop
        $existingRating = Rating::where('user_id', $request->user()->id)
            ->where('shop_id', $shop->id)
            ->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->update(['stars' => $validated['stars']]);
            return new RatingResource($existingRating->load(['user', 'shop']));
        }

        // Create new rating
        $rating = Rating::create([
            'user_id' => $request->user()->id,
            'shop_id' => $shop->id,
            'stars' => $validated['stars'],
        ]);

        return new RatingResource($rating->load(['user', 'shop']));
    }

    /**
     * Get ratings for a shop
     */
    public function index(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
        ]);

        $ratings = Rating::where('shop_id', $request->shop_id)
            ->with(['user', 'shop'])
            ->latest()
            ->paginate(20);

        return RatingResource::collection($ratings);
    }

    /**
     * Get current user's ratings
     */
    public function myRatings(Request $request)
    {
        $ratings = Rating::where('user_id', $request->user()->id)
            ->with(['user', 'shop'])
            ->latest()
            ->paginate(20);

        return RatingResource::collection($ratings);
    }
}
