<?php

namespace App\Filament\Resources\Activations\Pages;

use App\Filament\Resources\Activations\ActivationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActivations extends ListRecords
{
    protected static string $resource = ActivationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
