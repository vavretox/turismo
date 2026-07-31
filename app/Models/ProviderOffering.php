<?php

namespace App\Models;

use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderOffering extends Model
{
    use ResolvesImageUrl;

    protected $fillable = ['tourism_service_provider_id', 'titulo', 'resumen', 'descripcion', 'imagen', 'galeria', 'duracion', 'precio', 'incluye', 'destination_ids', 'activo', 'orden'];
    protected $casts = ['galeria' => 'array', 'destination_ids' => 'array', 'activo' => 'boolean', 'orden' => 'integer'];

    public function provider(): BelongsTo { return $this->belongsTo(TourismServiceProvider::class, 'tourism_service_provider_id'); }
    public function getImagenUrlAttribute(): ?string { return $this->resolveImageUrl($this->imagen); }
    public function getGaleriaUrlsAttribute(): array { return collect($this->galeria ?? [])->map(fn ($image) => $this->resolveImageUrl($image))->filter()->all(); }
}
