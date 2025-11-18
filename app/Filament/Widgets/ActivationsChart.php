<?php

namespace App\Filament\Widgets;

use App\Models\Activation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ActivationsChart extends ChartWidget
{
    protected ?string $heading = '📊 Daily Activations (Last 30 Days)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = $this->getActivationsPerDay();

        return [
            'datasets' => [
                [
                    'label' => '⚡ Activations',
                    'data' => $data['counts'],
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'pointBackgroundColor' => '#f59e0b',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getActivationsPerDay(): array
    {
        $labels = [];
        $counts = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');

            $count = Activation::whereDate('created_at', $date)
                ->count();

            $counts[] = $count;
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }
}
