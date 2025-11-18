<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'discount_percent' => (float) $this->discount_percent,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            'location' => [
                'latitude' => (float) $this->lat,
                'longitude' => (float) $this->lng,
            ],
            'neighborhood' => $this->when($this->relationLoaded('neighborhood') && $this->neighborhood, [
                'id' => $this->neighborhood?->id,
                'name' => $this->neighborhood?->name,
                'city' => $this->neighborhood?->city,
            ]),
            'category' => $this->when($this->relationLoaded('category') && $this->category, [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ]),
            'rating' => [
                'average' => round($this->ratings->avg('stars') ?? 0, 1),
                'count' => $this->relationLoaded('ratings') ? $this->ratings->count() : 0,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
