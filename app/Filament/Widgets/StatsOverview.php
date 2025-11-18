<?php

namespace App\Filament\Widgets;

use App\Models\Activation;
use App\Models\Shop;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Active Subscribers
        $activeSubscribers = Subscription::where('status', 'active')
            ->where('end_at', '>', now())
            ->count();

        // Activations This Month
        $activationsThisMonth = Activation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Activations Last Month
        $activationsLastMonth = Activation::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        // Revenue This Month (activations * 300 DZD)
        $revenueThisMonth = $activationsThisMonth * 300;

        // Top-Rated Shop
        $topRatedShop = Shop::select('shops.*')
            ->join('ratings', 'shops.id', '=', 'ratings.shop_id')
            ->groupBy('shops.id')
            ->selectRaw('AVG(ratings.stars) as avg_rating')
            ->orderByDesc('avg_rating')
            ->first();

        $topRatedShopName = $topRatedShop
            ? $topRatedShop->name . ' (' . number_format($topRatedShop->avg_rating, 1) . '⭐)'
            : 'No ratings yet';

        // Calculate trend
        $activationsTrend = $activationsLastMonth > 0
            ? (($activationsThisMonth - $activationsLastMonth) / $activationsLastMonth) * 100
            : 0;

        return [
            Stat::make('👥 Active Subscribers', $activeSubscribers)
                ->description('Currently active memberships')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->icon('heroicon-o-users')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition',
                ]),

            Stat::make('⚡ Activations This Month', $activationsThisMonth)
                ->description($activationsTrend >= 0 ? "↑ {$activationsTrend}% increase from last month" : "↓ " . abs($activationsTrend) . "% decrease from last month")
                ->descriptionIcon($activationsTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($activationsTrend >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-bolt')
                ->chart([3, 5, 10, 15, 12, 18, 20, $activationsThisMonth])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition',
                ]),

            Stat::make('💰 Revenue This Month', number_format($revenueThisMonth) . ' DZD')
                ->description('From ' . $activationsThisMonth . ' activations × 300 DZD')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->icon('heroicon-o-currency-dollar')
                ->chart([900, 1500, 3000, 4500, 3600, 5400, 6000, $revenueThisMonth])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition',
                ]),

            Stat::make('⭐ Top-Rated Shop', $topRatedShopName)
                ->description('Highest customer satisfaction')
                ->descriptionIcon('heroicon-m-star')
                ->color('info')
                ->icon('heroicon-o-trophy')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:shadow-lg transition',
                ]),
        ];
    }
}
