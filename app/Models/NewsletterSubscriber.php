<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'nombre', 'token', 'activo', 'suscrito_en', 'cancelado_en'];

    protected $casts = [
        'activo' => 'boolean',
        'suscrito_en' => 'datetime',
        'cancelado_en' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $subscriber): void {
            $subscriber->token ??= (string) Str::uuid();
            $subscriber->suscrito_en ??= now();
        });

        static::saving(function (self $subscriber): void {
            if ($subscriber->activo) {
                $subscriber->cancelado_en = null;
                $subscriber->suscrito_en ??= now();
            } elseif ($subscriber->isDirty('activo')) {
                $subscriber->cancelado_en ??= now();
            }
        });
    }
}
