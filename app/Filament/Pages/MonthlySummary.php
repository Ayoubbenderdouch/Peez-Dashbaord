<?php

namespace App\Filament\Pages;

use App\Models\Activation;
use App\Models\Shop;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class MonthlySummary extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.pages.monthly-summary';

    protected static ?string $navigationLabel = '📊 Monthly Summary';

    protected static ?int $navigationSort = 1;

    public ?string $selectedMonth = null;
    public array $shopData = [];
    public array $neighborhoodSummary = [];
    public array $categorySummary = [];

    public function mount(): void
    {
        $this->selectedMonth = now()->format('Y-m');
        $this->loadData();
    }

    public function updatedSelectedMonth(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        $startDate = \Carbon\Carbon::parse($this->selectedMonth . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Per Shop Data
        $this->shopData = Shop::with(['neighborhood', 'category', 'activations', 'ratings'])
            ->get()
            ->map(function ($shop) use ($startDate, $endDate) {
                $activationsCount = $shop->activations()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $revenueDzd = $activationsCount * 300;

                $avgStars = round($shop->ratings()->avg('stars') ?? 0, 2);

                return [
                    'shop_name' => $shop->name,
                    'neighborhood' => $shop->neighborhood->name,
                    'category' => $shop->category->name,
                    'activations_count' => $activationsCount,
                    'revenue_dzd' => $revenueDzd,
                    'avg_stars' => $avgStars,
                ];
            })
            ->toArray();

        // Per Neighborhood Summary
        $this->neighborhoodSummary = collect($this->shopData)
            ->groupBy('neighborhood')
            ->map(function ($shops, $neighborhood) {
                return [
                    'neighborhood' => $neighborhood,
                    'total_activations' => collect($shops)->sum('activations_count'),
                    'total_revenue' => collect($shops)->sum('revenue_dzd'),
                    'avg_rating' => round(collect($shops)->avg('avg_stars'), 2),
                    'shops_count' => count($shops),
                ];
            })
            ->values()
            ->toArray();

        // Per Category Summary
        $this->categorySummary = collect($this->shopData)
            ->groupBy('category')
            ->map(function ($shops, $category) {
                return [
                    'category' => $category,
                    'total_activations' => collect($shops)->sum('activations_count'),
                    'total_revenue' => collect($shops)->sum('revenue_dzd'),
                    'avg_rating' => round(collect($shops)->avg('avg_stars'), 2),
                    'shops_count' => count($shops),
                ];
            })
            ->values()
            ->toArray();
    }

    public function exportShopsCsv()
    {
        $filename = 'monthly_summary_shops_' . $this->selectedMonth . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Shop', 'Neighborhood', 'Category', 'Activations', 'Revenue (DZD)', 'Avg Stars']);

            foreach ($this->shopData as $row) {
                fputcsv($file, [
                    $row['shop_name'],
                    $row['neighborhood'],
                    $row['category'],
                    $row['activations_count'],
                    $row['revenue_dzd'],
                    $row['avg_stars'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportNeighborhoodsCsv()
    {
        $filename = 'monthly_summary_neighborhoods_' . $this->selectedMonth . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Neighborhood', 'Shops Count', 'Total Activations', 'Total Revenue (DZD)', 'Avg Rating']);

            foreach ($this->neighborhoodSummary as $row) {
                fputcsv($file, [
                    $row['neighborhood'],
                    $row['shops_count'],
                    $row['total_activations'],
                    $row['total_revenue'],
                    $row['avg_rating'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCategoriesCsv()
    {
        $filename = 'monthly_summary_categories_' . $this->selectedMonth . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Category', 'Shops Count', 'Total Activations', 'Total Revenue (DZD)', 'Avg Rating']);

            foreach ($this->categorySummary as $row) {
                fputcsv($file, [
                    $row['category'],
                    $row['shops_count'],
                    $row['total_activations'],
                    $row['total_revenue'],
                    $row['avg_rating'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
