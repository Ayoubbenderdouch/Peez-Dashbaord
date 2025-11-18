<?php

namespace App\Filament\Pages;

use App\Models\Activation;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class VendorActivation extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.pages.vendor-activation';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $navigationLabel = '⚡ Activate Subscription';

    protected static ?string $title = '⚡ Vendor Activation';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Activate User Subscription')
                    ->description('Create or extend a user subscription by activating through a shop.')
                    ->schema([
                        TextInput::make('user_uuid')
                            ->label('User UUID')
                            ->required()
                            ->placeholder('e.g., 550e8400-e29b-41d4-a716-446655440000')
                            ->helperText('Enter the UUID of the user to activate')
                            ->maxLength(36),

                        Radio::make('months')
                            ->label('Subscription Duration')
                            ->required()
                            ->options([
                                1 => '1 Month (300 DZD)',
                                2 => '2 Months (600 DZD)',
                                3 => '3 Months (900 DZD)',
                            ])
                            ->default(1)
                            ->inline()
                            ->helperText('Select subscription duration'),

                        Select::make('shop_id')
                            ->label('Shop (Optional)')
                            ->relationship('shop', 'name', function ($query) {
                                $user = Auth::user();
                                // Vendors can only see their own shops
                                if ($user && $user->role === 'vendor') {
                                    return $query->where('vendor_id', $user->id);
                                }
                                // Admin and Managers see all shops
                                return $query;
                            })
                            ->searchable()
                            ->preload()
                            ->helperText(function () {
                                $user = Auth::user();
                                if ($user && $user->role === 'vendor') {
                                    return 'Select one of your shops';
                                }
                                return 'Select a shop or leave empty';
                            })
                            ->placeholder('Select shop...'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function activateSubscription(): void
    {
        $data = $this->form->getState();

        // Find user by UUID
        $user = User::where('uuid', $data['user_uuid'])->first();

        if (!$user) {
            Notification::make()
                ->title('User Not Found')
                ->body('No user found with the provided UUID.')
                ->danger()
                ->send();
            return;
        }

        // Get shop (from form or vendor's first shop)
        $shopId = $data['shop_id'] ?? null;
        
        if (!$shopId) {
            // Try to get vendor's first shop
            $vendor = Auth::user();
            $vendorShop = Shop::where('vendor_id', $vendor->id)->first();
            
            if ($vendorShop) {
                $shop = $vendorShop;
            } else {
                // Fallback to any shop (for admin/manager)
                $shop = Shop::first();
            }
        } else {
            $shop = Shop::find($shopId);
        }

        if (!$shop) {
            Notification::make()
                ->title('Shop Not Found')
                ->body('Please select a valid shop.')
                ->danger()
                ->send();
            return;
        }

        // Get vendor (current user or fallback)
        $vendor = Auth::user();

        // Get or create subscription
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_at', '>', now())
            ->first();

        $months = (int) $data['months'];

        if ($subscription) {
            // Extend existing subscription
            $subscription->end_at = $subscription->end_at->addMonths($months);
            $subscription->save();
            $action = 'extended';
        } else {
            // Create new subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'start_at' => now(),
                'end_at' => now()->addMonths($months),
                'status' => 'active',
                'source' => 'vendor',
            ]);
            $action = 'activated';
        }

        // Create activation log
        Activation::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'vendor_id' => $vendor->id,
            'months' => $months,
            // amount_dzd will be auto-calculated in the model
        ]);

        // Send success notification
        Notification::make()
            ->title('Subscription ' . ucfirst($action))
            ->body("User {$user->name}'s subscription has been {$action} for {$months} month(s). Amount: " . ($months * 300) . " DZD")
            ->success()
            ->send();

        // Reset form
        $this->form->fill();
    }
}
