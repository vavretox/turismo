<?php

namespace App\Filament\Resources\TourismServiceProviderResource\Pages;

use App\Filament\Resources\TourismServiceProviderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTourismServiceProvider extends EditRecord
{
    protected static string $resource = TourismServiceProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
