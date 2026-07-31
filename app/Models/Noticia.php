<?php

namespace App\Models;

use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Noticia extends Model
{
    use HasSlug;
    use ResolvesImageUrl;

    protected $fillable = [
        'destino_id', 'titulo', 'slug', 'resumen', 'contenido', 'fuente_nombre', 'fuente_url',
        'imagen', 'publicado_en', 'activo',
        'newsletter_enviado_en', 'newsletter_destinatarios', 'newsletter_fallidos',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'publicado_en' => 'datetime',
        'newsletter_enviado_en' => 'datetime',
        'newsletter_destinatarios' => 'integer',
        'newsletter_fallidos' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('titulo')->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImagenUrlAttribute(): ?string
    {
        return $this->resolveImageUrl(
            $this->imagen,
            'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80',
        );
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class);
    }
}
