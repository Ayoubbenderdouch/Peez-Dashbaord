<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('phone')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'manager' => 'warning',
                        'vendor' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_vendor')
                    ->label('Vendor')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('uuid')
                    ->label('UUID')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(8)
                    ->tooltip(fn ($record) => $record->uuid),

                TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('subscriptions_count')
                    ->label('Subscriptions')
                    ->counts('subscriptions')
                    ->sortable()
                    ->toggleable(),

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
                SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'manager' => 'Manager',
                        'vendor' => 'Vendor',
                    ]),

                TernaryFilter::make('is_vendor')
                    ->label('Vendor Status')
                    ->placeholder('All users')
                    ->trueLabel('Vendors only')
                    ->falseLabel('Non-vendors only'),
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
            ->defaultSort('created_at', 'desc');
    }
}
