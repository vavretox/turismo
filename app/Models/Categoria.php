<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Categoria extends Model
{
    use HasSlug;

    protected $fillable = ['nombre', 'slug', 'descripcion', 'color', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('nombre')->saveSlugsTo('slug');
    }

    public function destinos(): HasMany
    {
        return $this->hasMany(Destino::class);
    }
}
