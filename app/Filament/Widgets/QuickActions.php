<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static ?string $heading = 'Quick Actions';
    
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -1; // Display at top

    protected function getViewData(): array
    {
        return [];
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament.widgets.quick-actions');
    }
}
