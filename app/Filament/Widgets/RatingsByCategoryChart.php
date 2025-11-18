<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Rating;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RatingsByCategoryChart extends ChartWidget
{
    protected ?string $heading = '⭐ Average Rating by Category';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = $this->getRatingsByCategory();

        return [
            'datasets' => [
                [
                    'label' => '⭐ Average Stars',
                    'data' => $data['ratings'],
                    'backgroundColor' => [
                        'rgba(245, 158, 11, 0.8)',  // Amber
                        'rgba(59, 130, 246, 0.8)',   // Blue
                        'rgba(16, 185, 129, 0.8)',   // Green
                        'rgba(239, 68, 68, 0.8)',    // Red
                        'rgba(139, 92, 246, 0.8)',   // Purple
                        'rgba(236, 72, 153, 0.8)',   // Pink
                        'rgba(20, 184, 166, 0.8)',   // Teal
                        'rgba(249, 115, 22, 0.8)',   // Orange
                        'rgba(6, 182, 212, 0.8)',    // Cyan
                        'rgba(132, 204, 22, 0.8)',   // Lime
                        'rgba(168, 85, 247, 0.8)',   // Violet
                        'rgba(244, 63, 94, 0.8)',    // Rose
                    ],
                    'borderColor' => [
                        '#f59e0b',
                        '#3b82f6',
                        '#10b981',
                        '#ef4444',
                        '#8b5cf6',
                        '#ec4899',
                        '#14b8a6',
                        '#f97316',
                        '#06b6d4',
                        '#84cc16',
                        '#a855f7',
                        '#f43f5e',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getRatingsByCategory(): array
    {
        $categories = Category::select('categories.name')
            ->join('shops', 'categories.id', '=', 'shops.category_id')
            ->join('ratings', 'shops.id', '=', 'ratings.shop_id')
            ->groupBy('categories.id', 'categories.name')
            ->selectRaw('AVG(ratings.stars) as avg_rating')
            ->get();

        $labels = [];
        $ratings = [];

        foreach ($categories as $category) {
            $labels[] = $category->name;
            $ratings[] = round($category->avg_rating, 2);
        }

        return [
            'labels' => $labels,
            'ratings' => $ratings,
        ];
    }
}
