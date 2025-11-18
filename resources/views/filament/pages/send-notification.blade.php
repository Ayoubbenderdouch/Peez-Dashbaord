<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Send Push Notification
        </x-slot>

        <x-slot name="description">
            Compose and send push notifications to active subscribers by segment
        </x-slot>

        <form wire:submit="sendNotification">
            {{ $this->form }}

            <div class="mt-6 flex justify-end gap-3">
                <x-filament::button
                    type="button"
                    color="gray"
                    wire:click="$set('data', [])"
                >
                    Reset
                </x-filament::button>

                <x-filament::button
                    type="submit"
                    color="primary"
                    icon="heroicon-o-paper-airplane"
                >
                    Send Notification
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    <x-filament::section class="mt-6">
        <x-slot name="heading">
            📊 Notification Statistics
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                <div class="text-sm text-green-600 dark:text-green-400 font-medium">Total Sent Today</div>
                <div class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">
                    {{ \App\Models\NotificationLog::whereDate('created_at', today())->count() }}
                </div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">This Week</div>
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">
                    {{ \App\Models\NotificationLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}
                </div>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
                <div class="text-sm text-purple-600 dark:text-purple-400 font-medium">Active FCM Tokens</div>
                <div class="text-2xl font-bold text-purple-700 dark:text-purple-300 mt-1">
                    {{ \App\Models\User::whereNotNull('fcm_token')->count() }}
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>
