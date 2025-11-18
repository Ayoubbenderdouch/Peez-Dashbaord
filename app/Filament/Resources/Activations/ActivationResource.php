<?php

namespace App\Filament\Resources\Activations;

use App\Filament\Resources\Activations\Pages\CreateActivation;
use App\Filament\Resources\Activations\Pages\EditActivation;
use App\Filament\Resources\Activations\Pages\ListActivations;
use App\Filament\Resources\Activations\Schemas\ActivationForm;
use App\Filament\Resources\Activations\Tables\ActivationsTable;
use App\Models\Activation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ActivationResource extends Resource
{
    protected static ?string $model = Activation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ActivationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivations::route('/'),
            'create' => CreateActivation::route('/create'),
            'edit' => EditActivation::route('/{record}/edit'),
        ];
    }
}
