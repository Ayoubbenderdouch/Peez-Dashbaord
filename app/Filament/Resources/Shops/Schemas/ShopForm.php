<?php

namespace App\Filament\Resources\Shops\Schemas;

use Filament\Forms\Get;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class ShopForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Shop Information')
                    ->description('Basic shop details')
                    ->schema([
                        TextInput::make('name')
                            ->label('🏪 Shop Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('neighborhood_id')
                            ->label('📍 Neighborhood')
                            ->relationship('neighborhood', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('ONE shop per category per neighborhood rule applies'),

                        Select::make('vendor_id')
                            ->label('👤 Vendor (Optional)')
                            ->relationship('vendor', 'name', fn ($query) => $query->where('is_vendor', true))
                            ->searchable()
                            ->preload()
                            ->helperText('Assign a vendor to this shop'),

                        Select::make('category_id')
                            ->label('🏷️ Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Must be unique per neighborhood')
                            ->reactive()
                            ->rules([
                                'required',
                                function (Get $get) {
                                    return Rule::unique('shops', 'category_id')
                                        ->where(function ($query) use ($get) {
                                            return $query->where('neighborhood_id', $get('neighborhood_id'));
                                        })
                                        ->ignore($get('id'));
                                },
                            ])
                            ->validationMessages([
                                'unique' => 'A shop with this category already exists in the selected neighborhood. Only ONE shop per category per neighborhood is allowed.',
                            ]),

                        TextInput::make('phone')
                            ->label('📞 Phone')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('+213XXXXXXXXX'),

                        TextInput::make('discount_percent')
                            ->label('💰 Discount (%)')
                            ->required()
                            ->numeric()
                            ->minValue(5)
                            ->maxValue(8)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Must be between 5.00% and 8.00%')
                            ->rules(['required', 'numeric', 'min:5', 'max:8']),

                        Toggle::make('is_active')
                            ->label('✅ Active')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Location')
                    ->description('GPS coordinates')
                    ->schema([
                        TextInput::make('lat')
                            ->label('🌍 Latitude')
                            ->required()
                            ->numeric()
                            ->step(0.0000001)
                            ->placeholder('35.6969744')
                            ->helperText('Oran latitude range: ~35.6 to 35.8'),

                        TextInput::make('lng')
                            ->label('🌍 Longitude')
                            ->required()
                            ->numeric()
                            ->step(0.0000001)
                            ->placeholder('-0.6331195')
                            ->helperText('Oran longitude range: ~-0.7 to -0.5'),
                    ])
                    ->columns(2),
            ]);
    }
}
