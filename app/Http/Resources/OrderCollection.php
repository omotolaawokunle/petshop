<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = OrderResource::class;

    public LengthAwarePaginator $originalResource;

    /**
     * @param  LengthAwarePaginator $resource
     */
    public function __construct(LengthAwarePaginator $resource)
    {
        $this->originalResource = $resource;

        parent::__construct($resource);
    }
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var array $originalResource */
        $originalResource = $this->originalResource->toArray();
        $originalResource['data'] = $this->collection;
        return $originalResource;
    }
}
