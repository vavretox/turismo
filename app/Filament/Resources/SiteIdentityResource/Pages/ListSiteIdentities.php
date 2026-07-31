<?php

namespace App\Filament\Resources\SiteIdentityResource\Pages;

use App\Filament\Resources\SiteIdentityResource;
use Filament\Resources\Pages\ListRecords;

class ListSiteIdentities extends ListRecords
{
    protected static string $resource = SiteIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
