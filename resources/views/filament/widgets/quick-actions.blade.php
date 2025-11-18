<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            ⚡ Quick Actions
        </x-slot>

        <x-slot name="description">
            Frequently used actions for quick access
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Add Shop --}}
            <a 
                href="{{ route('filament.admin.resources.shops.create') }}"
                class="flex flex-col items-center p-6 bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-lg border-2 border-amber-200 dark:border-amber-800 hover:shadow-lg hover:scale-105 transition-all duration-200 group"
            >
                <div class="flex items-center justify-center w-16 h-16 bg-amber-500 rounded-full mb-3 group-hover:bg-amber-600 transition-colors">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-amber-900 dark:text-amber-100 mb-1">
                    Add Shop
                </h3>
                <p class="text-sm text-amber-700 dark:text-amber-300 text-center">
                    Register a new shop in the system
                </p>
            </a>

            {{-- Activate Subscription --}}
            <a 
                href="{{ route('filament.admin.pages.vendor-activation') }}"
                class="flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg border-2 border-green-200 dark:border-green-800 hover:shadow-lg hover:scale-105 transition-all duration-200 group"
            >
                <div class="flex items-center justify-center w-16 h-16 bg-green-500 rounded-full mb-3 group-hover:bg-green-600 transition-colors">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-green-900 dark:text-green-100 mb-1">
                    Activate Subscription
                </h3>
                <p class="text-sm text-green-700 dark:text-green-300 text-center">
                    Activate or extend user membership
                </p>
            </a>

            {{-- Send Campaign --}}
            <a 
                href="{{ route('filament.admin.pages.send-notification') }}"
                class="flex flex-col items-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg border-2 border-blue-200 dark:border-blue-800 hover:shadow-lg hover:scale-105 transition-all duration-200 group"
            >
                <div class="flex items-center justify-center w-16 h-16 bg-blue-500 rounded-full mb-3 group-hover:bg-blue-600 transition-colors">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100 mb-1">
                    Send Campaign
                </h3>
                <p class="text-sm text-blue-700 dark:text-blue-300 text-center">
                    Send push notifications to users
                </p>
            </a>
        </div>

        {{-- Secondary Actions --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a 
                href="{{ route('filament.admin.pages.monthly-summary') }}"
                class="flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
            >
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Monthly Report</span>
            </a>

            <a 
                href="{{ route('filament.admin.pages.expiring-soon') }}"
                class="flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
            >
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Expiring Soon</span>
            </a>

            <a 
                href="{{ route('filament.admin.resources.subscriptions.index') }}"
                class="flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
            >
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Subscriptions</span>
            </a>

            <a 
                href="{{ route('filament.admin.resources.activations.index') }}"
                class="flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
            >
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Activations Log</span>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
