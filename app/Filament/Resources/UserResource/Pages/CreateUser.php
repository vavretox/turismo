<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_admin'] = ($data['role'] ?? 'user') === 'admin';
        $data['admin_sections'] = $data['role'] === 'user' ? ($data['admin_sections'] ?? []) : [];

        return $data;
    }
}
