<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * FCM Server Key (should be in .env as FCM_SERVER_KEY)
     */
    private string $fcmServerKey;

    /**
     * FCM API Endpoint
     */
    private const FCM_ENDPOINT = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->fcmServerKey = config('services.fcm.server_key', '');
    }

    /**
     * Send notification to a single user
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): bool
    {
        if (!$user->fcm_token) {
            Log::warning("User {$user->id} has no FCM token");
            return false;
        }

        return $this->send([$user->fcm_token], $title, $body, $data, $user->id);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers(Collection $users, string $title, string $body, array $data = []): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        foreach ($users as $user) {
            if (!$user->fcm_token) {
                $results['skipped']++;
                continue;
            }

            if ($this->sendToUser($user, $title, $body, $data)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Send notification to all active subscribers
     */
    public function sendToActiveSubscribers(string $title, string $body, array $data = []): array
    {
        $users = User::whereHas('subscriptions', function ($query) {
            $query->where('status', 'active')
                ->where('end_at', '>', now());
        })
            ->whereNotNull('fcm_token')
            ->get();

        return $this->sendToUsers($users, $title, $body, $data);
    }

    /**
     * Send notification to users in specific neighborhood
     */
    public function sendToNeighborhood(int $neighborhoodId, string $title, string $body, array $data = []): array
    {
        $users = User::whereHas('subscriptions', function ($query) {
            $query->where('status', 'active')
                ->where('end_at', '>', now());
        })
            ->whereNotNull('fcm_token')
            ->get();

        return $this->sendToUsers($users, $title, $body, $data);
    }

    /**
     * Send notification to users interested in specific category
     */
    public function sendToCategory(int $categoryId, string $title, string $body, array $data = []): array
    {
        $users = User::whereHas('subscriptions', function ($query) {
            $query->where('status', 'active')
                ->where('end_at', '>', now());
        })
            ->whereNotNull('fcm_token')
            ->get();

        return $this->sendToUsers($users, $title, $body, $data);
    }

    /**
     * Send notification to users who have rated a specific shop
     */
    public function sendToShop(int $shopId, string $title, string $body, array $data = []): array
    {
        $users = User::whereHas('ratings', function ($query) use ($shopId) {
            $query->where('shop_id', $shopId);
        })
            ->whereHas('subscriptions', function ($query) {
                $query->where('status', 'active')
                    ->where('end_at', '>', now());
            })
            ->whereNotNull('fcm_token')
            ->get();

        return $this->sendToUsers($users, $title, $body, $data);
    }

    /**
     * Send notification using FCM
     */
    private function send(array $tokens, string $title, string $body, array $data = [], ?int $userId = null): bool
    {
        // Stub implementation - replace with actual FCM integration
        if (empty($this->fcmServerKey)) {
            Log::info('FCM notification stub', [
                'tokens' => $tokens,
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);

            // Log to database
            $this->logNotification($userId, $title, $body, $data, 'success');

            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->fcmServerKey,
                'Content-Type' => 'application/json',
            ])->post(self::FCM_ENDPOINT, [
                'registration_ids' => $tokens,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'badge' => '1',
                ],
                'data' => array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]),
                'priority' => 'high',
            ]);

            $success = $response->successful();
            $status = $success ? 'success' : 'failed';

            // Log to database
            $this->logNotification($userId, $title, $body, $data, $status, $response->json());

            return $success;
        } catch (\Exception $e) {
            Log::error('FCM notification failed', [
                'error' => $e->getMessage(),
                'tokens' => $tokens,
            ]);

            $this->logNotification($userId, $title, $body, $data, 'failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Log notification to database
     */
    private function logNotification(?int $userId, string $title, string $body, array $data, string $status, ?array $response = null): void
    {
        NotificationLog::create([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'status' => $status,
            'response' => $response,
            'sent_at' => now(),
        ]);
    }

    /**
     * Notification templates
     */
    public static function templates(): array
    {
        return [
            'subscription_activated' => [
                'title' => 'Subscription Activated!',
                'body' => 'Your PEEZ membership has been activated. Enjoy exclusive discounts at local shops!',
                'data' => ['type' => 'subscription_activated'],
            ],
            'subscription_expiring_soon' => [
                'title' => 'Membership Expiring Soon',
                'body' => 'Your PEEZ membership expires in {days} days. Renew now to keep your discounts!',
                'data' => ['type' => 'subscription_expiring'],
            ],
            'subscription_expired' => [
                'title' => 'Membership Expired',
                'body' => 'Your PEEZ membership has expired. Renew now to regain access to exclusive discounts!',
                'data' => ['type' => 'subscription_expired'],
            ],
            'new_shop_nearby' => [
                'title' => 'New Shop Alert!',
                'body' => 'A new shop just joined PEEZ in your neighborhood: {shop_name}',
                'data' => ['type' => 'new_shop'],
            ],
            'promotional_campaign' => [
                'title' => 'Special Offer!',
                'body' => '{message}',
                'data' => ['type' => 'campaign'],
            ],
        ];
    }
}
