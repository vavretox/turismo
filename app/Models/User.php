<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
        'admin_sections',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'admin_sections' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['admin', 'user'], true);
    }

    public function tourismServiceProvider(): HasOne { return $this->hasOne(TourismServiceProvider::class); }

    public function isAdministrator(): bool
    {
        return $this->role === 'admin';
    }

    public function canAccessAdminSection(string $section): bool
    {
        return $this->isAdministrator()
            || in_array($section, $this->admin_sections ?? [], true);
    }

    public function canAccessAdminResource(string $resource): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        foreach (config('admin_sections', []) as $key => $definition) {
            if (($definition['resource'] ?? null) === $resource) {
                return $this->canAccessAdminSection($key);
            }
        }

        return false;
    }
}
