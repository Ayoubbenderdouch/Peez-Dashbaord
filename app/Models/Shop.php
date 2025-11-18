<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'neighborhood_id',
        'category_id',
        'vendor_id',
        'name',
        'discount_percent',
        'lat',
        'lng',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function activations(): HasMany
    {
        return $this->hasMany(Activation::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function averageRating()
    {
        return $this->ratings()->avg('stars');
    }
}
