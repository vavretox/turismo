<?php

namespace App\Models;

use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ProvinciaTuristica extends Model
{
    use HasSlug;
    use ResolvesImageUrl;

    protected $table = 'provincias_turisticas';

    protected $fillable = [
        'nombre',
        'slug',
        'provincia',
        'subtitulo',
        'resumen',
        'descripcion',
        'imagen',
        'imagenes_secundarias',
        'atractivos',
        'fiestas',
        'recomendaciones',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
        'imagenes_secundarias' => 'array',
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
            'https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=1600&q=85',
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

    public function municipiosLista(): array
    {
        return $this->linesToArray($this->municipios);
    }

    public function atractivosLista(): array
    {
        return $this->linesToArray($this->atractivos);
    }

    public function fiestasLista(): array
    {
        return $this->linesToArray($this->fiestas);
    }

    public function recomendacionesLista(): array
    {
        return $this->linesToArray($this->recomendaciones);
    }

    public function destinos(): HasMany
    {
        return $this->hasMany(Destino::class, 'municipio_id');
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'municipio_id');
    }

    public function actividadesSemanales(): HasMany
    {
        return $this->hasMany(WeeklyActivity::class, 'municipio_id');
    }

    private function linesToArray(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
