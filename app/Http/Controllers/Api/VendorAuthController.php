<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VendorAuthController extends Controller
{
    /**
     * Vendor POS Login
     * 
     * Authenticates vendor users for POS system access.
     * Returns token + shopId for subsequent API calls.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Validate credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Verify user has vendor role
        if ($user->role !== 'vendor') {
            return response()->json([
                'type' => 'https://peez.dz/errors/forbidden',
                'title' => 'Forbidden',
                'status' => 403,
                'detail' => 'Access denied. This endpoint is only available for vendor accounts.',
                'code' => 'VENDOR_ROLE_REQUIRED',
            ], 403);
        }

        // Verify vendor has an associated shop
        $shop = $user->shop;
        if (!$shop) {
            return response()->json([
                'type' => 'https://peez.dz/errors/configuration',
                'title' => 'Configuration Error',
                'status' => 424,
                'detail' => 'Your vendor account is not linked to any shop. Please contact support.',
                'code' => 'SHOP_NOT_CONFIGURED',
            ], 424);
        }

        // Create Sanctum token with vendor abilities
        $token = $user->createToken('vendor-pos-token', ['vendor:activate', 'vendor:view'])->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'tokenType' => 'Bearer',
                'expiresIn' => null, // Sanctum tokens don't expire by default
                'vendor' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'shop' => [
                    'id' => $shop->id,
                    'name' => $shop->name,
                    'categoryId' => $shop->category_id,
                    'categoryName' => $shop->category->name_ar ?? $shop->category->name_fr,
                    'neighborhoodId' => $shop->neighborhood_id,
                    'neighborhoodName' => $shop->neighborhood->name_ar ?? $shop->neighborhood->name_fr,
                    'discountPercent' => (float) $shop->discount_percent,
                ],
            ],
        ], 200);
    }

    /**
     * Vendor Logout
     * 
     * Revokes the current vendor access token.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'data' => [
                'message' => 'Successfully logged out',
            ],
        ], 200);
    }

    /**
     * Get Current Vendor Info
     * 
     * Returns the authenticated vendor's information and shop details.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $shop = $user->shop;

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
                'vendor' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'createdAt' => $user->created_at->toIso8601String(),
                ],
                'shop' => [
                    'id' => $shop->id,
                    'name' => $shop->name,
                    'categoryId' => $shop->category_id,
                    'categoryName' => $shop->category->name_ar ?? $shop->category->name_fr,
                    'neighborhoodId' => $shop->neighborhood_id,
                    'neighborhoodName' => $shop->neighborhood->name_ar ?? $shop->neighborhood->name_fr,
                    'discountPercent' => (float) $shop->discount_percent,
                    'address' => $shop->address,
                    'phone' => $shop->phone,
                    'latitude' => $shop->latitude ? (float) $shop->latitude : null,
                    'longitude' => $shop->longitude ? (float) $shop->longitude : null,
                ],
            ],
        ], 200);
    }
}
