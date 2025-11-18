<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('start_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('source')
                    ->searchable(),
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
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('extend')
                    ->label('⏳ Extend')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->color('success')
                    ->visible(fn (Subscription $record) => $record->status === 'active')
                    ->form([
                        Radio::make('months')
                            ->label('Extend Duration')
                            ->required()
                            ->options([
                                1 => '1 Month (+300 DZD)',
                                2 => '2 Months (+600 DZD)',
                                3 => '3 Months (+900 DZD)',
                            ])
                            ->default(1)
                            ->inline(),
                    ])
                    ->action(function (Subscription $record, array $data) {
                        $months = (int) $data['months'];
                        $record->end_at = $record->end_at->addMonths($months);
                        $record->save();

                        Notification::make()
                            ->title('Subscription Extended')
                            ->body("Subscription extended by {$months} month(s). New end date: " . $record->end_at->format('Y-m-d H:i'))
                            ->success()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('❌ Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Subscription $record) => $record->status === 'active')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Subscription')
                    ->modalDescription('Are you sure you want to cancel this subscription? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, Cancel')
                    ->action(function (Subscription $record) {
                        $record->status = 'cancelled';
                        $record->save();

                        Notification::make()
                            ->title('Subscription Cancelled')
                            ->body('The subscription has been cancelled successfully.')
                            ->warning()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
