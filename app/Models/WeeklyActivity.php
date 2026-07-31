<?php

namespace App\Models;

use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class WeeklyActivity extends Model
{
    use HasSlug;
    use ResolvesImageUrl;

    protected $fillable = [
        'municipio_id', 'titulo', 'slug', 'subtitulo', 'descripcion', 'contenido', 'sectores_interes', 'imagen', 'galeria', 'video',
        'lugar', 'horarios', 'direccion', 'mapa_url', 'telefono', 'whatsapp', 'correo',
        'sitio_web', 'facebook', 'instagram', 'x_url', 'youtube_url', 'fecha_actividad',
        'visible_desde', 'visible_hasta', 'texto_boton', 'enlace', 'orden', 'activo',
    ];

    protected $casts = [
        'fecha_actividad' => 'datetime',
        'visible_desde' => 'datetime',
        'visible_hasta' => 'datetime',
        'activo' => 'boolean',
        'orden' => 'integer',
        'galeria' => 'array',
        'sectores_interes' => 'array',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('titulo')->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $field ??= $this->getRouteKeyName();

        $activity = $this->where($field, $value)->first();

        if ($activity || $field !== 'slug') {
            return $activity;
        }

        $legacySlug = preg_replace('/-\d+$/', '', (string) $value);

        return $this->where('slug', $legacySlug)->first();
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('activo', true)
            ->where(fn (Builder $q) => $q->whereNull('visible_desde')->orWhere('visible_desde', '<=', now()))
            ->where(fn (Builder $q) => $q->whereNull('visible_hasta')->orWhere('visible_hasta', '>=', now()));
    }

    public function getImagenUrlAttribute(): string
    {
        $url = $this->resolveImageUrl($this->imagen, asset('images/referencia/tarija.jpg'));

        if ($this->updated_at) {
            $url .= str_contains($url, '?') ? '&' : '?';
            $url .= 'v='.$this->updated_at->timestamp;
        }

        return $url;
    }

    public function getEnlaceUrlAttribute(): ?string
    {
        if (blank($this->enlace)) {
            return route('actividades.show', $this);
        }

        $enlace = trim($this->enlace);

        if (Str::startsWith($enlace, ['/', '#', 'mailto:', 'tel:'])) {
            return $enlace;
        }

        if (! Str::startsWith($enlace, ['http://', 'https://'])) {
            return url('/'.ltrim($enlace, '/'));
        }

        $host = parse_url($enlace, PHP_URL_HOST);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true) || ($appHost && $host === $appHost)) {
            $path = parse_url($enlace, PHP_URL_PATH) ?: '/';
            $query = parse_url($enlace, PHP_URL_QUERY);
            $fragment = parse_url($enlace, PHP_URL_FRAGMENT);

            $publicPath = '/'.trim((string) parse_url(config('app.url'), PHP_URL_PATH), '/');
            if ($publicPath !== '/' && Str::startsWith($path, $publicPath.'/')) {
                $path = Str::after($path, $publicPath);
            }

            return $path
                .($query ? '?'.$query : '')
                .($fragment ? '#'.$fragment : '');
        }

        return $enlace;
    }

    public function getGaleriaUrlsAttribute(): Collection
    {
        return collect($this->galeria)
            ->filter()
            ->map(fn (string $image): string => $this->resolveImageUrl($image));
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->video);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(ProvinciaTuristica::class, 'municipio_id');
    }
}
