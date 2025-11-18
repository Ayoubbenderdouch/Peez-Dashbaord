<?php

namespace App\Filament\Resources\Activations\Pages;

use App\Filament\Resources\Activations\ActivationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditActivation extends EditRecord
{
    protected static string $resource = ActivationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
