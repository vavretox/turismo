<?php

namespace App\Filament\Resources\PortalImageResource\Pages;

use App\Filament\Resources\PortalImageResource;
use App\Models\PortalImage;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPortalImages extends ListRecords
{
    protected static string $resource = PortalImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Agregar fondo')
                ->visible(fn (): bool => PortalImage::query()->where('clave', 'like', 'home_hero_%')->count() < 5),
        ];
    }
}
