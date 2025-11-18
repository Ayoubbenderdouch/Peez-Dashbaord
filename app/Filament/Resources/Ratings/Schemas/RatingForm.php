<?php

namespace App\Filament\Resources\Ratings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RatingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('shop_id')
                    ->relationship('shop', 'name')
                    ->required(),
                TextInput::make('stars')
                    ->required()
                    ->numeric(),
            ]);
    }
}
