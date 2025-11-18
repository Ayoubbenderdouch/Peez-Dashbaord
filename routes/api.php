<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NeighborhoodController;
use App\Http\Controllers\Api\VendorAuthController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\VendorShopController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminShopController;
use App\Http\Controllers\Api\AdminNeighborhoodController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes v1
|--------------------------------------------------------------------------
*/

// Public Routes - No Authentication Required
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    
    // Vendor Authentication
    Route::post('/auth/vendor/login', [VendorAuthController::class, 'login']);
    
    // Public Data
    Route::get('/neighborhoods', [NeighborhoodController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    
    // User Card (Public - accessible via UUID)
    Route::get('/users/{uuid}/card', [UserController::class, 'card']);
    Route::post('/users/verify-qr', [UserController::class, 'verifyQrCode']);
    
    // Payment Webhooks
    Route::post('/webhooks/slickpay', function() {
        return response()->json(['message' => 'Webhook processed successfully']);
    });
    Route::post('/webhooks/cib', function() {
        return response()->json(['message' => 'Webhook processed successfully']);
    });
});

// Protected Routes - Sanctum Authentication Required
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/fcm-token', [AuthController::class, 'updateFcmToken']);
    
    // Shops
    Route::get('/shops', [ShopController::class, 'index']);
    Route::get('/shops/{id}', [ShopController::class, 'show']);
    Route::get('/shops/nearby', [ShopController::class, 'nearby']);
    Route::get('/shops/neighborhood/{neighborhoodId}', [ShopController::class, 'byNeighborhood']);
    Route::get('/shops/category/{categoryId}', [ShopController::class, 'byCategory']);
    
    // Subscriptions
    Route::get('/subscriptions/status', [SubscriptionController::class, 'status']);
    Route::get('/subscriptions/history', [SubscriptionController::class, 'history']);
    Route::post('/subscriptions/activate', [SubscriptionController::class, 'activate']);
    
    // Ratings
    Route::post('/ratings', [RatingController::class, 'rate']);
    Route::get('/ratings', [RatingController::class, 'index']);
    Route::get('/ratings/my-ratings', [RatingController::class, 'myRatings']);
    
    // Vendor POS Routes (requires vendor role)
    Route::middleware('role:vendor')->prefix('vendor')->group(function () {
        // Subscription Activation
        Route::post('/activate', [VendorController::class, 'activate']);
        Route::get('/activations', [VendorController::class, 'activations']);
        Route::get('/users/{uuid}/status', [VendorController::class, 'userStatus']);

        // Shop Management
        Route::get('/shop', [VendorShopController::class, 'getShop']);
        Route::put('/shop/discount', [VendorShopController::class, 'updateDiscount']);
        Route::put('/shop/info', [VendorShopController::class, 'updateShopInfo']);
        Route::put('/shop/status', [VendorShopController::class, 'updateShopStatus']);
        Route::get('/shop/stats', [VendorShopController::class, 'getStats']);
        Route::get('/shop/ratings', [VendorShopController::class, 'getRatings']);

        // Auth
        Route::get('/me', [VendorAuthController::class, 'me']);
        Route::post('/logout', [VendorAuthController::class, 'logout']);
    });

    // Admin Routes (requires admin or manager role)
    Route::middleware('role:admin,manager')->prefix('admin')->group(function () {
        // Shop Management
        Route::get('/shops', [AdminShopController::class, 'index']);
        Route::get('/shops/{id}', [AdminShopController::class, 'show']);
        Route::post('/shops', [AdminShopController::class, 'store']);
        Route::put('/shops/{id}', [AdminShopController::class, 'update']);
        Route::delete('/shops/{id}', [AdminShopController::class, 'destroy']);
        Route::get('/shops/vendors/available', [AdminShopController::class, 'availableVendors']);

        // Neighborhood Management
        Route::get('/neighborhoods', [AdminNeighborhoodController::class, 'index']);
        Route::get('/neighborhoods/{id}', [AdminNeighborhoodController::class, 'show']);
        Route::post('/neighborhoods', [AdminNeighborhoodController::class, 'store']);
        Route::put('/neighborhoods/{id}', [AdminNeighborhoodController::class, 'update']);
        Route::delete('/neighborhoods/{id}', [AdminNeighborhoodController::class, 'destroy']);
    });
});
