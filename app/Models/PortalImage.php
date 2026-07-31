<?php

namespace App\Models;

use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Model;

class PortalImage extends Model
{
    use ResolvesImageUrl;

    protected $fillable = ['nombre', 'clave', 'imagen', 'descripcion', 'activo', 'orden'];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public function getImagenUrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->imagen);
    }
}
