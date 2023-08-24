<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return in_array($this->orderStatus->title, ['shipped', 'paid']) ?
            $this->resource->toArray() :
            collect($this->resource)->except('payment')->toArray();
    }
}
