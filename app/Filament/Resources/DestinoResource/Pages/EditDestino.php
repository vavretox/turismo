<?php

namespace App\Filament\Resources\DestinoResource\Pages;

use App\Filament\Resources\DestinoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDestino extends EditRecord
{
    protected static string $resource = DestinoResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
