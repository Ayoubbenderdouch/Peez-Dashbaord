<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                DateTimePicker::make('start_at')
                    ->required(),
                DateTimePicker::make('end_at')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                TextInput::make('source')
                    ->required()
                    ->default('vendor'),
            ]);
    }
}
