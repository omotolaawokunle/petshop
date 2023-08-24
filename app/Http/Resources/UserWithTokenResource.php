<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWithTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'address' => $this->address,
            'avatar' => $this->avatar,
            'phone_number' => $this->phone_number,
            'is_marketing' => $this->is_marketing ? 1 : 0,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'token' => $this->resource->createToken('user-auth')
        ];
    }
}
