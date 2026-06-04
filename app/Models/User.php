<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Translatable\HasTranslations;

class User extends Authenticatable implements FilamentUser
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
     * Get the articles for the user/author.
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'user_id');
    }
}

