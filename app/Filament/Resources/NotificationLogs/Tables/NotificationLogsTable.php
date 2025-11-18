<?php

namespace App\Filament\Resources\NotificationLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class NotificationLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->placeholder('All Users (Segment)'),

                TextColumn::make('segment')
                    ->label('Segment')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'all_active_subscribers' => 'success',
                        'neighborhood' => 'info',
                        'category' => 'warning',
                        'shop' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('Individual User'),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title),

                TextColumn::make('body')
                    ->label('Message')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->body),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('Y-m-d H:i:s')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                        'pending' => 'Pending',
                    ]),

                SelectFilter::make('segment')
                    ->options([
                        'all_active_subscribers' => 'All Active Subscribers',
                        'neighborhood' => 'Neighborhood',
                        'category' => 'Category',
                        'shop' => 'Shop',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
