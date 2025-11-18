<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('User Information')
                    ->description('Basic user account details')
                    ->schema([
                        TextInput::make('name')
                            ->label('👤 Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('📞 Phone')
                            ->tel()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('+213XXXXXXXXX')
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('📧 Email Address')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label('🔒 Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->revealable(),

                        TextInput::make('fcm_token')
                            ->label('🔔 FCM Token')
                            ->maxLength(255)
                            ->placeholder('Firebase Cloud Messaging token')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Fieldset::make('Role & Permissions')
                    ->schema([
                        Select::make('role')
                            ->label('🎭 Role')
                            ->required()
                            ->options([
                                'admin' => 'Admin',
                                'manager' => 'Manager',
                                'vendor' => 'Vendor',
                            ])
                            ->default('vendor')
                            ->native(false),

                        Toggle::make('is_vendor')
                            ->label('💼 Is Vendor')
                            ->helperText('Can activate subscriptions?')
                            ->default(false),
                    ])
                    ->columns(2),

                Fieldset::make('System Information')
                    ->schema([
                        TextInput::make('uuid')
                            ->label('🔑 UUID')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated on creation')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
