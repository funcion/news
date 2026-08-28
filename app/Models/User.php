<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Translatable\HasTranslations;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    protected static function boot()
    {
        parent::boot();

        // Automatically assign default panel_user role on registration
        static::created(function ($user) {
            if (in_array($user->email, ['sifuncion@gmail.com', 'admin@glodaxia.com', 'luis.figuera@glodaxia.com'])) {
                $user->assignRole('super_admin');
            } elseif ($user->roles()->count() === 0) {
                $user->assignRole('panel_user');
            }
        });

        // Automatically purge avatar from Cloudflare R2 when user is deleted
        static::deleted(function ($user) {
            if ($raw = $user->getRawOriginal('avatar_url')) {
                $r2PublicUrl = rtrim(config('filesystems.disks.r2.url') ?? env('R2_PUBLIC_URL', 'https://media.glodaxia.com'), '/');
                $cleanPath = ltrim(str_replace([$r2PublicUrl, 'https://media.glodaxia.com', 'storage/'], '', $raw), '/');
                try {
                    \Illuminate\Support\Facades\Storage::disk('r2')->delete($cleanPath);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not delete avatar from R2 on user deletion: {$e->getMessage()}");
                }
            }
        });
    }

    use HasFactory, Notifiable, HasTranslations, HasRoles;

    /**
     * The translatable attributes.
     */
    public array $translatable = ['name', 'bio'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'slug',
        'bio',
        'avatar_url',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Resolve Avatar URL ensuring Cloudflare R2 CDN is always used.
     */
    public function getAvatarUrlAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        $r2PublicUrl = rtrim(config('filesystems.disks.r2.url') ?? env('R2_PUBLIC_URL', 'https://media.glodaxia.com'), '/');
        $cleanPath = ltrim(str_replace('storage/', '', $value), '/');

        return "{$r2PublicUrl}/{$cleanPath}";
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'bio' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Super admins and staff members
        if ($this->hasAnyRole(['super_admin', 'admin', 'redactor'])) {
            return true;
        }

        // Hardcoded root admin safety check
        if (in_array($this->email, ['sifuncion@gmail.com', 'admin@glodaxia.com', 'luis.figuera@glodaxia.com'])) {
            return true;
        }

        return false;
    }

    /**
     * Get the 2 initials of the author (First Name + Last Name).
     */
    public function getInitialsAttribute(): string
    {
        $name = trim($this->name ?? '');
        if (empty($name)) {
            $raw = $this->getAttributes()['name'] ?? '';
            $decoded = json_decode($raw, true);
            $name = is_array($decoded) ? ($decoded['es'] ?? $decoded['en'] ?? '') : $raw;
        }

        if (empty($name)) {
            return 'GL';
        }

        $parts = preg_split('/\s+/', trim($name));
        if (count($parts) >= 2) {
            $first = mb_substr($parts[0], 0, 1, 'UTF-8');
            $last = mb_substr($parts[count($parts) - 1], 0, 1, 'UTF-8');
            return strtoupper($first . $last);
        }

        return strtoupper(mb_substr($name, 0, 2, 'UTF-8'));
    }

    /**
     * Get the articles for the user/author.
     */
        /**
     * Get a consistent, vibrant gradient background for author avatar badges based on user ID or name.
     */
    public function getAvatarColorAttribute(): string
    {
        $palettes = [
            'from-cyan-600 to-blue-600',
            'from-indigo-600 to-purple-600',
            'from-emerald-600 to-teal-600',
            'from-amber-600 to-orange-600',
            'from-rose-600 to-pink-600',
            'from-violet-600 to-fuchsia-600',
            'from-sky-600 to-cyan-700',
            'from-teal-600 to-emerald-700',
        ];

        $idx = abs(crc32($this->slug ?? $this->email ?? 'glodaxia')) % count($palettes);
        return $palettes[$idx];
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'user_id');
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function commentLikes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CommentLike::class);
    }
}
