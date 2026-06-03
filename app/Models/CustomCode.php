<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'content',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get active code for a specific location.
     */
    public static function getActive(string $location): ?string
    {
        return static::where('location', $location)
            ->where('is_active', true)
            ->value('content');
    }
}