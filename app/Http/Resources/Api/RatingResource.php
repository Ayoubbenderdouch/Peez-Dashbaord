<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stars' => $this->stars,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'shop' => [
                'id' => $this->shop->id,
                'name' => $this->shop->name,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
