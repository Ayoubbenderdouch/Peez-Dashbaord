<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Activation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VendorShopController extends Controller
{
    /**
     * Update Shop Discount Percentage
     *
     * Allows vendors to modify their shop's discount percentage.
     * Discount must be between 4% and 10%.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDiscount(Request $request)
    {
        $validated = $request->validate([
            'discount_percent' => 'required|numeric|min:4|max:10',
        ]);

        $vendor = $request->user();
        $shop = $vendor->shop;

        if (!$shop) {
            return response()->json([
                'type' => 'https://peez.dz/errors/configuration',
                'title' => 'Configuration Error',
                'status' => 424,
                'detail' => 'Your vendor account is not linked to any shop.',
                'code' => 'SHOP_NOT_CONFIGURED',
            ], 424);
        }

        // Store old discount for history
        $oldDiscount = $shop->discount_percent;

        // Update discount
        $shop->discount_percent = $validated['discount_percent'];
        $shop->save();

        return response()->json([
            'data' => [
                'shopId' => $shop->id,
                'shopName' => $shop->name,
                'discount' => [
                    'old' => $oldDiscount,
                    'new' => $shop->discount_percent,
                ],
                'updatedAt' => $shop->updated_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * Get Shop Information
     *
     * Returns current shop details for the authenticated vendor.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShop(Request $request)
    {
        $vendor = $request->user();
        $shop = $vendor->shop()->with(['neighborhood', 'category'])->first();

        if (!$shop) {
            return response()->json([
                'type' => 'https://peez.dz/errors/configuration',
                'title' => 'Configuration Error',
                'status' => 424,
                'detail' => 'Your vendor account is not linked to any shop.',
                'code' => 'SHOP_NOT_CONFIGURED',
            ], 424);
        }

        return response()->json([
            'data' => [
                'id' => $shop->id,
                'name' => $shop->name,
                'address' => $shop->address,
                'phone' => $shop->phone,
                'discountPercent' => $shop->discount_percent,
                'isActive' => (bool) $shop->is_active,
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
                'rating' => [
                    'average' => $shop->rating_average ?? 0,
                    'count' => $shop->rating_count ?? 0,
                ],
            ],
        ], 200);
    }

    /**
     * Get Shop Statistics & Dashboard Data
     *
     * Returns comprehensive statistics for vendor dashboard:
     * - Today's activations
     * - This month's revenue
     * - All-time statistics
     * - Recent activations
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        $vendor = $request->user();
        $shop = $vendor->shop;

        if (!$shop) {
            return response()->json([
                'type' => 'https://peez.dz/errors/configuration',
                'title' => 'Configuration Error',
                'status' => 424,
                'detail' => 'Your vendor account is not linked to any shop.',
                'code' => 'SHOP_NOT_CONFIGURED',
            ], 424);
        }

        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $monthStart = $now->copy()->startOfMonth();

        // Today's statistics
        $todayActivations = Activation::where('shop_id', $shop->id)
            ->whereDate('created_at', $today)
            ->count();
        $todayRevenue = $todayActivations * 300; // 300 DZD per activation

        // This month's statistics
        $monthActivations = Activation::where('shop_id', $shop->id)
            ->whereBetween('created_at', [$monthStart, $now])
            ->count();
        $monthRevenue = $monthActivations * 300;

        // All-time statistics
        $totalActivations = Activation::where('shop_id', $shop->id)->count();
        $totalRevenue = $totalActivations * 300;

        // Recent activations (last 10)
        $recentActivations = Activation::where('shop_id', $shop->id)
            ->with('user:id,uuid,name,email')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($activation) {
                return [
                    'id' => $activation->id,
                    'user' => [
                        'uuid' => $activation->user->uuid,
                        'name' => $activation->user->name,
                    ],
                    'months' => $activation->months,
                    'revenue' => 300,
                    'activatedAt' => $activation->created_at->toIso8601String(),
                ];
            });

        // Monthly breakdown (last 6 months)
        $monthlyBreakdown = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $count = Activation::where('shop_id', $shop->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $monthlyBreakdown[] = [
                'month' => $month->format('Y-m'),
                'monthName' => $month->translatedFormat('F Y'),
                'activations' => $count,
                'revenue' => $count * 300,
            ];
        }

        return response()->json([
            'data' => [
                'shop' => [
                    'id' => $shop->id,
                    'name' => $shop->name,
                    'discountPercent' => $shop->discount_percent,
                ],
                'today' => [
                    'activations' => $todayActivations,
                    'revenue' => $todayRevenue,
                    'currency' => 'DZD',
                ],
                'thisMonth' => [
                    'activations' => $monthActivations,
                    'revenue' => $monthRevenue,
                    'currency' => 'DZD',
                ],
                'allTime' => [
                    'activations' => $totalActivations,
                    'revenue' => $totalRevenue,
                    'currency' => 'DZD',
                ],
                'recentActivations' => $recentActivations,
                'monthlyBreakdown' => $monthlyBreakdown,
            ],
        ], 200);
    }

    /**
     * Update Shop Information
     *
     * Allows vendors to update their shop name.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateShopInfo(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100',
        ]);

        $vendor = $request->user();
        $shop = $vendor->shop;

        if (!$shop) {
            return response()->json([
                'type' => 'https://peez.dz/errors/configuration',
                'title' => 'Configuration Error',
                'status' => 424,
                'detail' => 'Your vendor account is not linked to any shop.',
                'code' => 'SHOP_NOT_CONFIGURED',
            ], 424);
        }

        $oldName = $shop->name;
        $shop->name = $validated['name'];
        $shop->save();

        return response()->json([
            'data' => [
                'shopId' => $shop->id,
                'name' => [
                    'old' => $oldName,
                    'new' => $shop->name,
                ],
                'updatedAt' => $shop->updated_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * Update Shop Status (Open/Closed)
     *
     * Allows vendors to set their shop as open (ouvert) or closed (fermé).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateShopStatus(Request $request)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $vendor = $request->user();
        $shop = $vendor->shop;

        if (!$shop) {
            return response()->json([
                'type' => 'https://peez.dz/errors/configuration',
                'title' => 'Configuration Error',
                'status' => 424,
                'detail' => 'Your vendor account is not linked to any shop.',
                'code' => 'SHOP_NOT_CONFIGURED',
            ], 424);
        }

        $oldStatus = $shop->is_active;
        $shop->is_active = $validated['is_active'];
        $shop->save();

        return response()->json([
            'data' => [
                'shopId' => $shop->id,
                'shopName' => $shop->name,
                'status' => [
                    'old' => $oldStatus ? 'ouvert' : 'fermé',
                    'new' => $shop->is_active ? 'ouvert' : 'fermé',
                ],
                'isActive' => $shop->is_active,
                'updatedAt' => $shop->updated_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * Get Shop Ratings
     *
     * Returns all ratings for the vendor's shop.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRatings(Request $request)
    {
        $vendor = $request->user();
        $shop = $vendor->shop;

        if (!$shop) {
            return response()->json([
                'type' => 'https://peez.dz/errors/configuration',
                'title' => 'Configuration Error',
                'status' => 424,
                'detail' => 'Your vendor account is not linked to any shop.',
                'code' => 'SHOP_NOT_CONFIGURED',
            ], 424);
        }

        $ratings = DB::table('ratings')
            ->join('users', 'ratings.user_id', '=', 'users.id')
            ->where('ratings.shop_id', $shop->id)
            ->select('ratings.id', 'ratings.stars', 'ratings.created_at', 'users.name as user_name', 'users.uuid as user_uuid')
            ->orderBy('ratings.created_at', 'desc')
            ->get()
            ->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'stars' => $rating->stars,
                    'user' => [
                        'uuid' => $rating->user_uuid,
                        'name' => $rating->user_name,
                    ],
                    'createdAt' => Carbon::parse($rating->created_at)->toIso8601String(),
                ];
            });

        // Calculate statistics
        $totalRatings = $ratings->count();
        $averageRating = $totalRatings > 0 ? round($ratings->avg('stars'), 2) : 0;

        // Star distribution
        $starDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $ratings->where('stars', $i)->count();
            $percentage = $totalRatings > 0 ? round(($count / $totalRatings) * 100, 1) : 0;
            $starDistribution[] = [
                'stars' => $i,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return response()->json([
            'data' => [
                'shop' => [
                    'id' => $shop->id,
                    'name' => $shop->name,
                ],
                'statistics' => [
                    'totalRatings' => $totalRatings,
                    'averageRating' => $averageRating,
                    'starDistribution' => $starDistribution,
                ],
                'ratings' => $ratings,
            ],
        ], 200);
    }
}
