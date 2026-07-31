<?php

namespace App\Filament\Resources\PortalImageResource\Pages;

use App\Filament\Resources\PortalImageResource;
use Filament\Resources\Pages\EditRecord;

class EditPortalImage extends EditRecord
{
    protected static string $resource = PortalImageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
