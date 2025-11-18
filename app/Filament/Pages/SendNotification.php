<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Neighborhood;
use App\Models\Shop;
use App\Services\NotificationService;
use BackedEnum;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SendNotification extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?string $navigationLabel = '🔔 Send Notification';

    protected static ?string $title = '🔔 Send Push Notification';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.send-notification';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Notification Details')
                    ->description('Compose and send push notifications to users')
                    ->schema([
                        Radio::make('segment_type')
                            ->label('Send To')
                            ->required()
                            ->options([
                                'all' => 'All Active Subscribers',
                                'neighborhood' => 'Specific Neighborhood',
                                'category' => 'Specific Category',
                                'shop' => 'Specific Shop',
                            ])
                            ->default('all')
                            ->live()
                            ->columnSpanFull(),

                        Select::make('neighborhood_id')
                            ->label('Neighborhood')
                            ->relationship('neighborhood', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('segment_type') === 'neighborhood')
                            ->required(fn ($get) => $get('segment_type') === 'neighborhood'),

                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('segment_type') === 'category')
                            ->required(fn ($get) => $get('segment_type') === 'category'),

                        Select::make('shop_id')
                            ->label('Shop')
                            ->relationship('shop', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('segment_type') === 'shop')
                            ->required(fn ($get) => $get('segment_type') === 'shop'),
                    ])
                    ->columns(2),

                Section::make('Message Content')
                    ->schema([
                        Select::make('template')
                            ->label('Template (Optional)')
                            ->options([
                                'activated' => 'Subscription Activated',
                                'expiring_soon' => 'Expiring Soon',
                                'campaign' => 'Campaign/Promotion',
                                'custom' => 'Custom Message',
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === 'activated') {
                                    $set('title', 'تفعيل الاشتراك / Subscription Activated');
                                    $set('body', 'تم تفعيل اشتراكك بنجاح! استمتع بخصوماتك الآن. / Your subscription has been activated successfully!');
                                } elseif ($state === 'expiring_soon') {
                                    $set('title', 'تذكير / Reminder');
                                    $set('body', 'اشتراكك سينتهي قريباً. قم بالتجديد الآن! / Your subscription is expiring soon. Renew now!');
                                } elseif ($state === 'campaign') {
                                    $set('title', 'عرض خاص / Special Offer');
                                    $set('body', 'عرض حصري لأعضاء PEEZ! / Exclusive offer for PEEZ members!');
                                } else {
                                    $set('title', '');
                                    $set('body', '');
                                }
                            })
                            ->columnSpanFull(),

                        TextInput::make('title')
                            ->label('Notification Title')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Enter notification title (Arabic/French)'),

                        Textarea::make('body')
                            ->label('Notification Body')
                            ->required()
                            ->rows(4)
                            ->maxLength(500)
                            ->placeholder('Enter notification message (Arabic/French)')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function sendNotification(): void
    {
        $data = $this->form->getState();
        $notificationService = app(NotificationService::class);

        $title = $data['title'];
        $body = $data['body'];
        $segmentType = $data['segment_type'];

        try {
            $results = match ($segmentType) {
                'all' => $notificationService->sendToActiveSubscribers($title, $body),
                'neighborhood' => $notificationService->sendToNeighborhood(
                    $data['neighborhood_id'],
                    $title,
                    $body
                ),
                'category' => $notificationService->sendToCategory(
                    $data['category_id'],
                    $title,
                    $body
                ),
                'shop' => $notificationService->sendToShop(
                    $data['shop_id'],
                    $title,
                    $body
                ),
                default => ['success' => 0, 'failed' => 0, 'skipped' => 0],
            };

            Notification::make()
                ->title('Notifications Sent')
                ->body("✅ Success: {$results['success']} | ❌ Failed: {$results['failed']} | ⏭️ Skipped: {$results['skipped']}")
                ->success()
                ->send();

            // Reset form
            $this->form->fill();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Notification Failed')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
