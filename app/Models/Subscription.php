<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_at',
        'end_at',
        'status',
        'source',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
    
    protected $appends = ['is_active'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && $this->end_at > now();
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_at > now();
    }

    public function isExpired(): bool
    {
        return $this->end_at < now() || $this->status === 'expired';
    }
}
