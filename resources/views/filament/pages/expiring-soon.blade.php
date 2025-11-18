<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Days Selector & Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">⏰ Expiring Soon</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Users with subscriptions expiring within <strong>{{ $days }} days</strong>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <select
                        wire:model.live="days"
                        class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                        <option value="3">Next 3 Days</option>
                        <option value="7">Next 7 Days</option>
                        <option value="14">Next 14 Days</option>
                        <option value="30">Next 30 Days</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl shadow p-6 border-2 border-orange-200 dark:border-orange-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Expiring Soon</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $expiringUsers->count() }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Active subscriptions</p>
                    </div>
                    <div class="text-5xl">⚠️</div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl shadow p-6 border-2 border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Urgent (≤3 days)</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                            {{ $expiringUsers->where('days_left', '<=', 3)->count() }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Needs immediate action</p>
                    </div>
                    <div class="text-5xl">🚨</div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl shadow p-6 border-2 border-green-200 dark:border-green-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Selected</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ count($selectedUsers) }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">For notification</p>
                    </div>
                    <div class="text-5xl">✅</div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">📋 Expiring Subscriptions</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Select users to send renewal reminders</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            wire:click="exportCsv"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export CSV
                        </button>
                        <button
                            wire:click="notifySelected"
                            @disabled(count($selectedUsers) === 0)
                            class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors shadow"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Notify Selected
                        </button>
                        <button
                            wire:click="notifyAll"
                            @disabled($expiringUsers->isEmpty())
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors shadow"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Notify All
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input
                                    type="checkbox"
                                    @if(!$expiringUsers->isEmpty())
                                        wire:click="$set('selectedUsers', {{ $expiringUsers->isEmpty() ? '[]' : json_encode($expiringUsers->pluck('user_id')->toArray()) }})"
                                    @endif
                                    class="rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                                    @disabled($expiringUsers->isEmpty())
                                >
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expires At</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Days Left</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($expiringUsers as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedUsers"
                                        value="{{ $user['user_id'] }}"
                                        class="rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                                    >
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $user['user_name'] }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">UUID: {{ Str::limit($user['user_uuid'], 12) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="text-gray-900 dark:text-white">📞 {{ $user['user_phone'] }}</div>
                                        @if($user['user_email'])
                                            <div class="text-gray-500 dark:text-gray-400">📧 {{ $user['user_email'] }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $user['end_at']->format('Y-m-d') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user['end_at']->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                        @if($user['days_left'] <= 1)
                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @elseif($user['days_left'] <= 3)
                                            bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                        @elseif($user['days_left'] <= 7)
                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @endif
                                    ">
                                        {{ $user['days_left'] }} {{ $user['days_left'] == 1 ? 'day' : 'days' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($user['days_left'] <= 1)
                                        <span class="text-2xl" title="Critical">🚨</span>
                                    @elseif($user['days_left'] <= 3)
                                        <span class="text-2xl" title="Urgent">⚠️</span>
                                    @elseif($user['days_left'] <= 7)
                                        <span class="text-2xl" title="Warning">⏰</span>
                                    @else
                                        <span class="text-2xl" title="OK">✅</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-400 dark:text-gray-600">
                                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-sm font-medium">No subscriptions expiring within {{ $days }} days</p>
                                        <p class="text-xs mt-1">All users are good for now!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
