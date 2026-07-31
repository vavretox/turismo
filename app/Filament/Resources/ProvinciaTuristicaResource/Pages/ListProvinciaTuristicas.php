<?php

namespace App\Filament\Resources\ProvinciaTuristicaResource\Pages;

use App\Filament\Resources\ProvinciaTuristicaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProvinciaTuristicas extends ListRecords
{
    protected static string $resource = ProvinciaTuristicaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
