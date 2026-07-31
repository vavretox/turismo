<?php

namespace App\Filament\Resources\SiteIdentityResource\Pages;

use App\Filament\Resources\SiteIdentityResource;
use Filament\Resources\Pages\EditRecord;

class EditSiteIdentity extends EditRecord
{
    protected static string $resource = SiteIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
