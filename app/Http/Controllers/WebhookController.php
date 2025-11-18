<?php

namespace App\Http\Controllers;

use App\Models\Activation;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Slick Pay payment webhook
     *
     * Slick Pay Documentation: https://docs.slickpay.dz
     *
     * Expected webhook payload:
     * {
     *   "transaction_id": "123456",
     *   "status": "success",
     *   "amount": 300,
     *   "currency": "DZD",
     *   "customer_id": "user_uuid_here",
     *   "metadata": {
     *     "months": 1,
     *     "shop_id": 1
     *   },
     *   "signature": "hmac_signature_here"
     * }
     */
    public function slickpay(Request $request)
    {
        // Log incoming webhook
        Log::info('Slick Pay webhook received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        // Verify webhook signature
        if (!$this->verifySlickPaySignature($request)) {
            Log::warning('Slick Pay webhook signature verification failed');
            return response()->json([
                'error' => 'Invalid signature'
            ], 401);
        }

        // Parse webhook data
        $transactionId = $request->input('transaction_id');
        $status = $request->input('status');
        $amount = $request->input('amount');
        $userUuid = $request->input('customer_id');
        $months = $request->input('metadata.months', 1);
        $shopId = $request->input('metadata.shop_id');

        // Only process successful payments
        if ($status !== 'success') {
            Log::info('Slick Pay payment not successful', [
                'transaction_id' => $transactionId,
                'status' => $status,
            ]);

            return response()->json([
                'message' => 'Payment not successful',
                'status' => $status
            ], 200);
        }

        // Find user by UUID
        $user = User::where('uuid', $userUuid)->first();

        if (!$user) {
            Log::error('User not found for Slick Pay payment', [
                'user_uuid' => $userUuid,
                'transaction_id' => $transactionId,
            ]);

            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        // Find shop if provided
        $shop = $shopId ? Shop::find($shopId) : Shop::first();

        if (!$shop) {
            Log::error('Shop not found for Slick Pay payment', [
                'shop_id' => $shopId,
                'transaction_id' => $transactionId,
            ]);

            return response()->json([
                'error' => 'Shop not found'
            ], 404);
        }

        // Get or create subscription
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_at', '>', now())
            ->first();

        if ($subscription) {
            // Extend existing subscription
            $subscription->end_at = $subscription->end_at->addMonths($months);
            $subscription->save();
            $action = 'extended';
        } else {
            // Create new subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'start_at' => now(),
                'end_at' => now()->addMonths($months),
                'status' => 'active',
                'source' => 'slickpay',
            ]);
            $action = 'created';
        }

        // Create activation log
        $activation = Activation::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'vendor_id' => $user->id, // Self-service payment
            'months' => $months,
            'amount_dzd' => $amount,
        ]);

        Log::info('Slick Pay payment processed successfully', [
            'transaction_id' => $transactionId,
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'activation_id' => $activation->id,
            'action' => $action,
        ]);

        // TODO: Send confirmation notification to user
        // NotificationService::sendToUser($user, ...)

        return response()->json([
            'message' => 'Payment processed successfully',
            'subscription_id' => $subscription->id,
            'activation_id' => $activation->id,
            'action' => $action,
        ], 200);
    }

    /**
     * Verify Slick Pay webhook signature
     *
     * @param Request $request
     * @return bool
     */
    private function verifySlickPaySignature(Request $request): bool
    {
        // Get Slick Pay secret key from environment
        $secretKey = config('services.slickpay.secret_key');

        if (empty($secretKey)) {
            Log::warning('Slick Pay secret key not configured');
            // In development, allow webhooks without signature
            return app()->environment('local', 'development');
        }

        // Get signature from header or payload
        $signature = $request->header('X-Slickpay-Signature')
            ?? $request->input('signature');

        if (empty($signature)) {
            return false;
        }

        // Compute expected signature
        // Slick Pay typically uses HMAC-SHA256
        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secretKey);

        // Compare signatures
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle CIB (Algerian bank) payment webhook
     *
     * CIB Documentation: https://www.cib.dz/documentation
     *
     * This is a stub for future CIB integration
     */
    public function cib(Request $request)
    {
        Log::info('CIB webhook received', [
            'payload' => $request->all(),
        ]);

        return response()->json([
            'message' => 'CIB webhook endpoint - not yet implemented'
        ], 501);
    }
}
