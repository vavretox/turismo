<?php

namespace App\Filament\Resources\EventoResource\Pages;

use App\Filament\Resources\EventoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvento extends EditRecord
{
    protected static string $resource = EventoResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
