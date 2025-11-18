<?php

namespace App\Filament\Resources\Neighborhoods\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NeighborhoodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('city')
                    ->required()
                    ->default('Oran'),
            ]);
    }
}
