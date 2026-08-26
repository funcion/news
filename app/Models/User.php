<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Translatable\HasTranslations;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable, HasTranslations;

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

    protected static function boot()
    {
        parent::boot();

        // Convert avatar to webp (100px) after upload
        static::saved(function ($user) {
            if (!$user->wasChanged('avatar_url') || !$user->avatar_url) return;

            $path = $user->avatar_url;
            $fullPath = storage_path('app/public/' . $path);

            if (!file_exists($fullPath) || pathinfo($path, PATHINFO_EXTENSION) === 'webp') return;

            try {
                $image = \Intervention\Image\Laravel\Facades\Image::read($fullPath);
                $newPath = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME) . '.webp';
                $newFullPath = storage_path('app/public/' . $newPath);

                $image->toWebp(90)->save($newFullPath);

                if ($newPath !== $path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                    \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->update(['avatar_url' => $newPath]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Avatar webp conversion failed: ' . $e->getMessage());
            }
        });
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
        return true;
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
}

