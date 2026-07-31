<?php

namespace App\Models;

use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Model;

class SiteIdentity extends Model
{
    use ResolvesImageUrl;

    protected $fillable = [
        'clave', 'nombre', 'logo', 'descripcion', 'fuente_texto', 'fuente_titulos',
        'tamano_texto', 'peso_texto', 'peso_titulos', 'peso_botones', 'espaciado_titulos',
        'facebook_url', 'instagram_url', 'x_url', 'youtube_url', 'tiktok_url', 'whatsapp_url',
    ];

    protected $casts = [
        'tamano_texto' => 'integer',
        'peso_texto' => 'integer',
        'peso_titulos' => 'integer',
        'peso_botones' => 'integer',
        'espaciado_titulos' => 'float',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->logo);
    }

    public function socialLinks(): array
    {
        return array_values(array_filter([
            ['label' => 'Facebook', 'url' => $this->facebook_url, 'icon' => 'fa-facebook'],
            ['label' => 'Instagram', 'url' => $this->instagram_url, 'icon' => 'fa-instagram'],
            ['label' => 'TikTok', 'url' => $this->tiktok_url, 'icon' => 'fa-tiktok'],
            ['label' => 'YouTube', 'url' => $this->youtube_url ?: 'https://www.youtube.com/', 'icon' => 'fa-youtube'],
            ['label' => 'X', 'url' => $this->x_url ?: 'https://x.com/', 'icon' => 'fa-x-twitter'],
            ['label' => 'WhatsApp', 'url' => $this->whatsapp_url, 'icon' => 'fa-whatsapp'],
        ], fn (array $link): bool => filled($link['url'])));
    }

}
