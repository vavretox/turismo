<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
class AttractionType extends Model {
    use HasSlug;
    protected $fillable = ['parent_id','nombre','slug','icono','color','descripcion','que_hacer','orden','activo'];
    protected $casts = ['activo'=>'boolean','orden'=>'integer'];
    public function getSlugOptions(): SlugOptions { return SlugOptions::create()->generateSlugsFrom('nombre')->saveSlugsTo('slug'); }
    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(self::class, 'parent_id')->orderBy('orden'); }
    public function places(): HasMany { return $this->hasMany(AttractionPlace::class); }
}
