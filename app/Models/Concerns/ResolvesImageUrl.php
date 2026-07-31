<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait ResolvesImageUrl
{
    public function resolveImageUrl(?string $value, ?string $fallback = null): ?string
    {
        if (blank($value)) {
            return $fallback;
        }

        if (Str::startsWith($value, ['http://', 'https://', '/'])) {
            return $value;
        }

        // Keep uploaded media on the same host used to open the portal. This
        // works both with turismo-page.test and with the LAN IP on mobile.
        return '/storage/'.ltrim(str_replace('\\', '/', $value), '/');
    }
}
