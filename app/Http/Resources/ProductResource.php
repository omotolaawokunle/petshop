<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category_uuid' => $this->category_uuid,
            'title' => $this->title,
            'uuid' => $this->uuid,
            'price' => (float) $this->price,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'category' => $this->whenLoaded('category', $this->category, null),
            'brand' => $this->whenAppended('brand', $this->brand, null),
        ];
    }
}
