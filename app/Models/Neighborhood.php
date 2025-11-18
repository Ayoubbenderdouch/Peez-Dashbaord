<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Neighborhood extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
    ];

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }
}
