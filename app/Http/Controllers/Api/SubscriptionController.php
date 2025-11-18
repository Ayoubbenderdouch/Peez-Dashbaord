<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SubscriptionResource;
use App\Models\Subscription;
use App\Models\Shop;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Get user's active subscriptions
     */
    public function status(Request $request)
    {
        $subscriptions = $request->user()
            ->subscriptions()
            ->with('shop.neighborhood')
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->get();

        return SubscriptionResource::collection($subscriptions);
    }

    /**
     * Get subscription history
     */
    public function history(Request $request)
    {
        $subscriptions = $request->user()
            ->subscriptions()
            ->with('shop.neighborhood')
            ->latest()
            ->paginate(20);

        return SubscriptionResource::collection($subscriptions);
    }

    /**
     * Activate a new subscription (vendor only)
     */
    public function activate(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'duration' => 'required|in:1,2,3', // months
            'payment_method' => 'required|in:slickpay,cib,cash',
            'payment_reference' => 'nullable|string',
        ]);

        $shop = Shop::findOrFail($validated['shop_id']);

        // Verify vendor owns this shop
        if ($shop->vendor_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You do not own this shop',
            ], 403);
        }

        // Check if already has active subscription
        $existingActive = Subscription::where('shop_id', $shop->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->exists();

        if ($existingActive) {
            return response()->json([
                'message' => 'This shop already has an active subscription',
            ], 422);
        }

        $subscription = Subscription::create([
            'user_id' => $request->user()->id,
            'shop_id' => $shop->id,
            'start_date' => now(),
            'end_date' => now()->addMonths($validated['duration']),
            'status' => 'active',
            'source' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
        ]);

        return new SubscriptionResource($subscription->load('shop.neighborhood'));
    }
}
