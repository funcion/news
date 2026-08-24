<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'locale',
        'token',
        'verified_at',
        'source',
        'ip_address',
        'user_agent',
        'unsubscribed_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Scope for verified subscribers
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Scope for active (verified and not unsubscribed) subscribers
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('verified_at')->whereNull('unsubscribed_at');
    }

    /**
     * Check if subscriber is active
     */
    public function isActive(): bool
    {
        return $this->verified_at !== null && $this->unsubscribed_at === null;
    }
}