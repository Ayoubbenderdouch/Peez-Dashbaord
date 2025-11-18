<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Activation;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorController extends Controller
{
    /**
     * Activate or Extend User Subscription
     * 
     * Processes subscription activation at vendor POS.
     * Supports idempotency to prevent duplicate charges.
     * 
     * Business Rules:
     * - Extends existing subscription if active
     * - Creates new subscription if none exists or expired
     * - Months must be 1, 2, or 3
     * - Requires Idempotency-Key header
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'userUuid' => 'required|uuid',
            'months' => 'required|integer|in:1,2,3',
        ]);

        // Check Idempotency-Key header
        $idempotencyKey = $request->header('Idempotency-Key');
        if (!$idempotencyKey) {
            return response()->json([
                'type' => 'https://peez.dz/errors/validation',
                'title' => 'Validation Error',
                'status' => 422,
                'detail' => 'Idempotency-Key header is required for this operation.',
                'code' => 'IDEMPOTENCY_KEY_REQUIRED',
            ], 422);
        }

        // Check if this idempotency key was already used
        $existingActivation = Activation::where('idempotency_key', $idempotencyKey)->first();
        if ($existingActivation) {
            // Return the original response (idempotent behavior)
            $subscription = Subscription::where('user_id', $existingActivation->user_id)->first();
            
            return response()->json([
                'data' => [
                    'activationId' => $existingActivation->id,
                    'userUuid' => User::find($existingActivation->user_id)->uuid,
                    'months' => $existingActivation->months,
                    'shopId' => $existingActivation->shop_id,
                    'activatedAt' => $existingActivation->created_at->toIso8601String(),
                    'subscription' => [
                        'startsAt' => $subscription->start_at->toIso8601String(),
                        'endsAt' => $subscription->end_at->toIso8601String(),
                        'isActive' => $subscription->is_active,
                    ],
                    'idempotent' => true,
                ],
            ], 200);
        }

        // Find user by UUID
        $user = User::where('uuid', $validated['userUuid'])->first();
        if (!$user) {
            return response()->json([
                'type' => 'https://peez.dz/errors/not-found',
                'title' => 'User Not Found',
                'status' => 404,
                'detail' => 'No user found with the provided UUID.',
                'code' => 'USER_NOT_FOUND',
                'key' => 'userUuid',
            ], 404);
        }

        // Get vendor's shop
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

        // Process activation within transaction
        DB::beginTransaction();
        try {
            // Check if user has existing subscription
            $subscription = Subscription::where('user_id', $user->id)->first();
            
            $now = Carbon::now();
            $monthsToAdd = (int) $validated['months'];
            
            if ($subscription && $subscription->is_active) {
                // Extend existing active subscription
                $subscription->end_at = Carbon::parse($subscription->end_at)->addMonths($monthsToAdd);
                $subscription->save();
                
                $startsAt = $subscription->start_at;
                $endsAt = $subscription->end_at;
                $action = 'extended';
            } else {
                // Create new subscription or reactivate expired one
                if ($subscription) {
                    $subscription->start_at = $now;
                    $subscription->end_at = $now->copy()->addMonths($monthsToAdd);
                    $subscription->status = 'active';
                    $subscription->save();
                } else {
                    $subscription = Subscription::create([
                        'user_id' => $user->id,
                        'start_at' => $now,
                        'end_at' => $now->copy()->addMonths($monthsToAdd),
                        'status' => 'active',
                    ]);
                }
                
                $startsAt = $subscription->start_at;
                $endsAt = $subscription->end_at;
                $action = 'created';
            }

            // Log the activation
            $activation = Activation::create([
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'vendor_id' => $vendor->id,
                'months' => $monthsToAdd,
                'idempotency_key' => $idempotencyKey,
            ]);

            DB::commit();

            return response()->json([
                'data' => [
                    'activationId' => $activation->id,
                    'userUuid' => $user->uuid,
                    'userName' => $user->name,
                    'months' => $monthsToAdd,
                    'shopId' => $shop->id,
                    'shopName' => $shop->name,
                    'activatedAt' => $activation->created_at->toIso8601String(),
                    'subscription' => [
                        'startsAt' => $startsAt->toIso8601String(),
                        'endsAt' => $endsAt->toIso8601String(),
                        'isActive' => true,
                        'action' => $action,
                    ],
                    'idempotent' => false,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'type' => 'https://peez.dz/errors/server',
                'title' => 'Server Error',
                'status' => 500,
                'detail' => 'An error occurred while processing the activation.',
                'code' => 'ACTIVATION_FAILED',
            ], 500);
        }
    }

    /**
     * Get Vendor Activation History
     * 
     * Returns list of activations performed by this vendor.
     * Supports filtering by month for revenue tracking.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activations(Request $request)
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

        // Build query
        $query = Activation::where('shop_id', $shop->id)
            ->with('user:id,uuid,name,email');

        // Filter by month if provided (format: YYYY-MM)
        if ($request->has('month')) {
            $monthFilter = $request->input('month');
            
            // Validate format
            if (!preg_match('/^\d{4}-\d{2}$/', $monthFilter)) {
                return response()->json([
                    'type' => 'https://peez.dz/errors/validation',
                    'title' => 'Validation Error',
                    'status' => 422,
                    'detail' => 'Month must be in YYYY-MM format.',
                    'code' => 'INVALID_MONTH_FORMAT',
                    'key' => 'month',
                ], 422);
            }

            $startOfMonth = Carbon::createFromFormat('Y-m', $monthFilter)->startOfMonth();
            $endOfMonth = Carbon::createFromFormat('Y-m', $monthFilter)->endOfMonth();

            $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        }

        // Get activations
        $activations = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $totalActivations = $activations->count();
        $totalRevenue = $totalActivations * 300; // 300 DZD per activation

        return response()->json([
            'data' => [
                'shop' => [
                    'id' => $shop->id,
                    'name' => $shop->name,
                ],
                'period' => $request->has('month') ? $request->input('month') : 'all-time',
                'statistics' => [
                    'totalActivations' => $totalActivations,
                    'totalRevenue' => $totalRevenue,
                    'currency' => 'DZD',
                    'pricePerActivation' => 300,
                ],
                'activations' => $activations->map(function ($activation) {
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
                }),
            ],
        ], 200);
    }

    /**
     * Check User Subscription Status
     * 
     * Quick validation endpoint for POS checkout.
     * Returns whether user has active subscription.
     * 
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function userStatus(Request $request, string $uuid)
    {
        // Find user by UUID
        $user = User::where('uuid', $uuid)->first();
        
        if (!$user) {
            return response()->json([
                'type' => 'https://peez.dz/errors/not-found',
                'title' => 'User Not Found',
                'status' => 404,
                'detail' => 'No user found with the provided UUID.',
                'code' => 'USER_NOT_FOUND',
            ], 404);
        }

        // Get subscription
        $subscription = Subscription::where('user_id', $user->id)->first();

        if (!$subscription) {
            return response()->json([
                'data' => [
                    'userUuid' => $user->uuid,
                    'userName' => $user->name,
                    'hasActiveSubscription' => false,
                    'subscription' => null,
                ],
            ], 200);
        }

        return response()->json([
            'data' => [
                'userUuid' => $user->uuid,
                'userName' => $user->name,
                'hasActiveSubscription' => $subscription->is_active,
                'subscription' => [
                    'startsAt' => $subscription->start_at->toIso8601String(),
                    'endsAt' => $subscription->end_at->toIso8601String(),
                    'isActive' => $subscription->is_active,
                    'daysRemaining' => $subscription->is_active 
                        ? Carbon::now()->diffInDays($subscription->end_at, false) 
                        : 0,
                ],
            ],
        ], 200);
    }
}
