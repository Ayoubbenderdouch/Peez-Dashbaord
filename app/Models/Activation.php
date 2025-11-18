<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'vendor_id',
        'months',
        'amount_dzd',
        'idempotency_key',
    ];

    protected $casts = [
        'months' => 'integer',
        'amount_dzd' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activation) {
            if (empty($activation->amount_dzd)) {
                $activation->amount_dzd = $activation->months * 300;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
