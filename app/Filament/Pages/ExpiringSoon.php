<?php

namespace App\Filament\Pages;

use App\Models\Subscription;
use App\Models\User;
use App\Services\NotificationService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class ExpiringSoon extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected string $view = 'filament.pages.expiring-soon';

    protected static ?string $navigationLabel = '⏰ Expiring Soon';

    protected static ?int $navigationSort = 2;

    public int $days = 7;
    public Collection $expiringUsers;
    public array $selectedUsers = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function updatedDays(): void
    {
        $this->loadData();
        $this->selectedUsers = [];
    }

    protected function loadData(): void
    {
        $endDate = now()->addDays($this->days);

        $this->expiringUsers = Subscription::with(['user'])
            ->where('status', 'active')
            ->whereBetween('end_at', [now(), $endDate])
            ->orderBy('end_at', 'asc')
            ->get()
            ->map(function ($subscription) {
                $daysLeft = now()->diffInDays($subscription->end_at, false);

                return [
                    'user_id' => $subscription->user_id,
                    'user_uuid' => $subscription->user->uuid,
                    'user_name' => $subscription->user->name,
                    'user_phone' => $subscription->user->phone,
                    'user_email' => $subscription->user->email,
                    'end_at' => $subscription->end_at,
                    'days_left' => ceil($daysLeft),
                    'subscription_id' => $subscription->id,
                ];
            });
    }

    public function exportCsv()
    {
        $filename = 'expiring_soon_' . $this->days . '_days_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['User UUID', 'Name', 'Phone', 'Email', 'Expires At', 'Days Left']);

            foreach ($this->expiringUsers as $user) {
                fputcsv($file, [
                    $user['user_uuid'],
                    $user['user_name'],
                    $user['user_phone'],
                    $user['user_email'],
                    $user['end_at']->format('Y-m-d H:i:s'),
                    $user['days_left'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function notifySelected()
    {
        if (empty($this->selectedUsers)) {
            Notification::make()
                ->warning()
                ->title('No Users Selected')
                ->body('Please select at least one user to notify.')
                ->send();
            return;
        }

        $notificationService = app(NotificationService::class);
        $userIds = $this->selectedUsers;

        try {
            // Get users from selected IDs
            $users = User::whereIn('id', $userIds)->get();

            foreach ($users as $user) {
                // Find the subscription to get days left
                $subscription = Subscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->first();

                if ($subscription) {
                    $daysLeft = now()->diffInDays($subscription->end_at, false);

                    $notificationService->sendToUser(
                        $user,
                        'subscription_expiring_soon',
                        ['days' => ceil($daysLeft)]
                    );
                }
            }

            Notification::make()
                ->success()
                ->title('Notifications Sent')
                ->body('Expiry reminders sent to ' . count($userIds) . ' user(s).')
                ->send();

            $this->selectedUsers = [];
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Notification Failed')
                ->body('Error: ' . $e->getMessage())
                ->send();
        }
    }

    public function notifyAll()
    {
        if ($this->expiringUsers->isEmpty()) {
            Notification::make()
                ->warning()
                ->title('No Users Found')
                ->body('No subscriptions expiring within ' . $this->days . ' days.')
                ->send();
            return;
        }

        $notificationService = app(NotificationService::class);
        $userIds = $this->expiringUsers->pluck('user_id')->toArray();

        try {
            $users = User::whereIn('id', $userIds)->get();

            foreach ($users as $user) {
                // Find the subscription to get days left
                $subscription = Subscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->first();

                if ($subscription) {
                    $daysLeft = now()->diffInDays($subscription->end_at, false);

                    $notificationService->sendToUser(
                        $user,
                        'subscription_expiring_soon',
                        ['days' => ceil($daysLeft)]
                    );
                }
            }

            Notification::make()
                ->success()
                ->title('Notifications Sent')
                ->body('Expiry reminders sent to all ' . count($userIds) . ' user(s).')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Notification Failed')
                ->body('Error: ' . $e->getMessage())
                ->send();
        }
    }
}
