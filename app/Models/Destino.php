<?php

namespace App\Models;

use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Destino extends Model implements HasMedia
{
    use HasSlug;
    use InteractsWithMedia;
    use ResolvesImageUrl;

    protected $fillable = [
        'categoria_id', 'municipio_id', 'nombre', 'slug', 'subtitulo', 'resumen', 'introduccion',
        'descripcion', 'como_llegar', 'rutas_llegada', 'mejor_epoca', 'duracion_recomendada',
        'recomendaciones', 'imagen', 'imagenes_secundarias', 'ubicacion', 'precio',
        'latitud', 'longitud', 'destacado', 'activo', 'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'destacado' => 'boolean',
        'precio' => 'decimal:2',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
        'imagenes_secundarias' => 'array',
        'rutas_llegada' => 'array',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('nombre')->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImagenUrlAttribute(): ?string
    {
        return $this->resolveImageUrl(
            $this->imagen,
            'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80',
        );
    }

    public function imagenesSecundariasUrls(): array
    {
        return collect($this->imagenes_secundarias ?? [])
            ->filter()
            ->map(fn (string $imagen) => $this->resolveImageUrl($imagen))
            ->values()
            ->all();
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(ProvinciaTuristica::class, 'municipio_id');
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class);
    }

    public function noticias(): HasMany
    {
        return $this->hasMany(Noticia::class);
    }
}
