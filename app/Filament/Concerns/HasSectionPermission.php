<?php

namespace App\Filament\Concerns;

trait HasSectionPermission
{
    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessAdminResource(static::class) === true;
    }
}
