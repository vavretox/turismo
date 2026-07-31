<?php

namespace App\Models;

use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Evento extends Model
{
    use HasSlug;
    use ResolvesImageUrl;

    protected $fillable = ['destino_id', 'municipio_id', 'titulo', 'slug', 'descripcion', 'imagen', 'lugar', 'fecha_inicio', 'fecha_fin', 'activo'];

    protected $casts = ['activo' => 'boolean', 'fecha_inicio' => 'datetime', 'fecha_fin' => 'datetime'];

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
            'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1200&q=80',
        );
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(ProvinciaTuristica::class, 'municipio_id');
    }
}
