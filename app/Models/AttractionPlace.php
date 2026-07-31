<?php
namespace App\Models;
use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
class AttractionPlace extends Model {
    use HasSlug, ResolvesImageUrl;
    protected $fillable = ['tourism_service_provider_id','attraction_type_id','titulo','slug','resumen','descripcion','imagen','galeria','latitud','longitud','direccion','telefono','whatsapp','sitio_web','facebook','instagram','tiktok','x_url','youtube_url','horario','precio','room_options','service_details','destacado','activo','orden'];
    protected $casts = ['latitud'=>'float','longitud'=>'float','galeria'=>'array','room_options'=>'array','service_details'=>'array','destacado'=>'boolean','activo'=>'boolean','orden'=>'integer'];
    public function getGaleriaUrlsAttribute(): array { return collect($this->galeria ?? [])->map(fn ($image) => $this->resolveImageUrl($image))->filter()->all(); }
    public function getSlugOptions(): SlugOptions { return SlugOptions::create()->generateSlugsFrom('titulo')->saveSlugsTo('slug'); }
    public function type(): BelongsTo { return $this->belongsTo(AttractionType::class, 'attraction_type_id'); }
    public function serviceProvider(): BelongsTo { return $this->belongsTo(TourismServiceProvider::class, 'tourism_service_provider_id'); }
    public function getImagenUrlAttribute(): string { return $this->resolveImageUrl($this->imagen, asset('images/referencia/tarija.jpg')); }
}
