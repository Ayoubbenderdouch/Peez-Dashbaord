<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class UserController extends Controller
{
    /**
     * Get User Membership Card
     * 
     * Returns user's digital membership card with QR code.
     * QR code contains signed payload for vendor verification.
     * 
     * Payload Structure:
     * {
     *   "uuid": "user-uuid",
     *   "expiresAt": "2025-12-31T23:59:59Z",
     *   "signature": "hmac-sha256-signature"
     * }
     * 
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function card(Request $request, string $uuid)
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

        // Generate membership ID (format: PEEZ-YYYYMMDD-XXXXX)
        $membershipId = 'PEEZ-' . $user->created_at->format('Ymd') . '-' . str_pad($user->id, 5, '0', STR_PAD_LEFT);

        // Prepare QR code payload
        $qrPayload = [
            'uuid' => $user->uuid,
            'membershipId' => $membershipId,
            'expiresAt' => $subscription && $subscription->is_active 
                ? $subscription->end_at->toIso8601String() 
                : null,
            'timestamp' => now()->timestamp,
        ];

        // Sign the payload
        $signature = hash_hmac('sha256', json_encode($qrPayload), config('app.key'));
        $qrPayload['signature'] = $signature;

        // Generate QR code as base64 data URL
        $options = new QROptions([
            'version'    => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_L,
            'scale'      => 6,
        ]);

        $qrCode = new QRCode($options);
        $qrCodeImage = $qrCode->render(json_encode($qrPayload));

        return response()->json([
            'data' => [
                'user' => [
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'membershipId' => $membershipId,
                    'memberSince' => $user->created_at->toIso8601String(),
                ],
                'subscription' => $subscription ? [
                    'isActive' => $subscription->is_active,
                    'startsAt' => $subscription->start_at->toIso8601String(),
                    'endsAt' => $subscription->end_at->toIso8601String(),
                    'daysRemaining' => $subscription->is_active 
                        ? now()->diffInDays($subscription->end_at, false) 
                        : 0,
                ] : [
                    'isActive' => false,
                    'startsAt' => null,
                    'endsAt' => null,
                    'daysRemaining' => 0,
                ],
                'qrCode' => [
                    'image' => $qrCodeImage, // Base64 data URL
                    'format' => 'image/png',
                    'payload' => $qrPayload, // For debugging (remove in production)
                ],
            ],
        ], 200);
    }

    /**
     * Verify QR Code Signature
     * 
     * Utility endpoint for vendors to verify QR code authenticity.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyQrCode(Request $request)
    {
        $validated = $request->validate([
            'payload' => 'required|string',
        ]);

        try {
            $payload = json_decode($validated['payload'], true);
            
            if (!$payload || !isset($payload['signature'], $payload['uuid'])) {
                return response()->json([
                    'data' => [
                        'valid' => false,
                        'reason' => 'Invalid payload structure',
                    ],
                ], 200);
            }

            // Extract signature
            $receivedSignature = $payload['signature'];
            unset($payload['signature']);

            // Recalculate signature
            $expectedSignature = hash_hmac('sha256', json_encode($payload), config('app.key'));

            // Verify signature
            if (!hash_equals($expectedSignature, $receivedSignature)) {
                return response()->json([
                    'data' => [
                        'valid' => false,
                        'reason' => 'Invalid signature',
                    ],
                ], 200);
            }

            // Check expiration
            if (isset($payload['expiresAt']) && $payload['expiresAt']) {
                $expiresAt = \Carbon\Carbon::parse($payload['expiresAt']);
                if ($expiresAt->isPast()) {
                    return response()->json([
                        'data' => [
                            'valid' => true,
                            'expired' => true,
                            'userUuid' => $payload['uuid'],
                            'expiresAt' => $payload['expiresAt'],
                        ],
                    ], 200);
                }
            }

            return response()->json([
                'data' => [
                    'valid' => true,
                    'expired' => false,
                    'userUuid' => $payload['uuid'],
                    'membershipId' => $payload['membershipId'] ?? null,
                    'expiresAt' => $payload['expiresAt'] ?? null,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'data' => [
                    'valid' => false,
                    'reason' => 'Invalid JSON payload',
                ],
            ], 200);
        }
    }
}
