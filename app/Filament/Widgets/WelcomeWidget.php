<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeWidget extends Widget
{
    protected static ?int $sort = -1;

    protected string $view = 'filament.widgets.welcome-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'user' => Auth::user(),
            'greeting' => $this->getGreeting(),
        ];
    }

    private function getGreeting(): string
    {
        $hour = now()->hour;

        if ($hour < 12) {
            return '🌅 Good Morning';
        } elseif ($hour < 18) {
            return '☀️ Good Afternoon';
        } else {
            return '🌙 Good Evening';
        }
    }
}
