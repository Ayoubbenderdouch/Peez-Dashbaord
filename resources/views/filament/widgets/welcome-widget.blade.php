<x-filament-widgets::widget>
    <x-filament::section class="!p-0 overflow-hidden">
        <!-- Header with Gradient Background -->
        <div class="relative bg-gradient-to-br from-amber-400 via-orange-500 to-red-500 p-8 overflow-hidden">
            <!-- Animated background patterns -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/3 translate-y-1/3"></div>
            </div>

            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center text-amber-600 text-4xl font-bold shadow-2xl transform hover:scale-105 transition-transform">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <div>
                        <h2 class="text-3xl font-extrabold text-white drop-shadow-lg">
                            {{ $greeting }}, {{ $user->name }}! 👋
                        </h2>
                        <p class="text-white/90 mt-2 text-base font-medium">
                            Welcome back to your <span class="font-bold">PEEZ Dashboard</span>
                        </p>
                        <div class="flex items-center gap-3 mt-3">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/30">
                                🎭 {{ ucfirst($user->role) }}
                            </span>
                            <span class="text-sm text-white/80 font-medium">
                                📧 {{ $user->email }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="hidden lg:flex items-center space-x-4">
                    <div class="text-center px-6 py-4 bg-white/20 backdrop-blur-md rounded-2xl border border-white/30 shadow-xl transform hover:scale-105 transition-transform">
                        <div class="text-3xl font-bold text-white">{{ now()->format('d') }}</div>
                        <div class="text-xs text-white/80 font-semibold mt-1">{{ now()->format('M Y') }}</div>
                    </div>
                    <div class="text-center px-6 py-4 bg-white/20 backdrop-blur-md rounded-2xl border border-white/30 shadow-xl transform hover:scale-105 transition-transform">
                        <div class="text-3xl font-bold text-white">{{ now()->format('H:i') }}</div>
                        <div class="text-xs text-white/80 font-semibold mt-1">{{ now()->format('l') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Cards -->
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">⚡ Quick Access</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Shops Card -->
                <a href="/admin/shops" class="group block">
                    <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-6 border-2 border-amber-200 dark:border-amber-800 hover:border-amber-400 dark:hover:border-amber-600 transition-all hover:shadow-xl hover:-translate-y-1">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-amber-200/20 rounded-full -translate-y-10 translate-x-10 group-hover:scale-150 transition-transform"></div>
                        <div class="relative z-10">
                            <div class="text-5xl mb-3">🏪</div>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-200">Shops</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage stores & discounts</p>
                        </div>
                    </div>
                </a>

                <!-- Users Card -->
                <a href="/admin/users" class="group block">
                    <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border-2 border-blue-200 dark:border-blue-800 hover:border-blue-400 dark:hover:border-blue-600 transition-all hover:shadow-xl hover:-translate-y-1">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-blue-200/20 rounded-full -translate-y-10 translate-x-10 group-hover:scale-150 transition-transform"></div>
                        <div class="relative z-10">
                            <div class="text-5xl mb-3">👥</div>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-200">Users</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage members & roles</p>
                        </div>
                    </div>
                </a>

                <!-- Activations Card -->
                <a href="/admin/vendor-activation" class="group block">
                    <div class="relative overflow-hidden bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 border-2 border-green-200 dark:border-green-800 hover:border-green-400 dark:hover:border-green-600 transition-all hover:shadow-xl hover:-translate-y-1">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-green-200/20 rounded-full -translate-y-10 translate-x-10 group-hover:scale-150 transition-transform"></div>
                        <div class="relative z-10">
                            <div class="text-5xl mb-3">⚡</div>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-200">Activations</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Activate subscriptions</p>
                        </div>
                    </div>
                </a>

                <!-- Analytics Card -->
                <a href="/admin" class="group block">
                    <div class="relative overflow-hidden bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6 border-2 border-purple-200 dark:border-purple-800 hover:border-purple-400 dark:hover:border-purple-600 transition-all hover:shadow-xl hover:-translate-y-1">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-purple-200/20 rounded-full -translate-y-10 translate-x-10 group-hover:scale-150 transition-transform"></div>
                        <div class="relative z-10">
                            <div class="text-5xl mb-3">📊</div>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-200">Analytics</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View statistics & reports</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
