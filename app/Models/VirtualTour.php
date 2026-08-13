<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VirtualTour extends Model
{
    protected $fillable = ['name', 'description', 'cover_image', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function scenes(): HasMany
    {
        return $this->hasMany(VirtualTourScene::class)->orderBy('sort_order')->orderBy('id');
    }
}
