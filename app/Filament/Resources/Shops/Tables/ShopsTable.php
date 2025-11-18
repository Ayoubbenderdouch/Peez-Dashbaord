<?php

namespace App\Filament\Resources\Shops\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ShopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Shop Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('neighborhood.name')
                    ->label('Neighborhood')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('discount_percent')
                    ->label('Discount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->suffix('%')
                    ->color(fn ($state) => $state >= 7 ? 'success' : ($state >= 6 ? 'warning' : 'danger')),

                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('averageRating')
                    ->label('Avg Rating')
                    ->getStateUsing(fn ($record) => number_format($record->averageRating() ?? 0, 1))
                    ->suffix(' ⭐')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('lat')
                    ->label('Latitude')
                    ->numeric(decimalPlaces: 7)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('lng')
                    ->label('Longitude')
                    ->numeric(decimalPlaces: 7)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('neighborhood_id')
                    ->label('Neighborhood')
                    ->relationship('neighborhood', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All shops')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
