<?php

namespace App\Models;

use App\Services\Traits\HasUuids;
use App\Services\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, HasUuids, Filterable;

    protected $guarded = [];

    protected $casts = ['address' => 'array', 'products' => 'array', 'shipped_at' => 'datetime'];

    protected $hidden = ['id', 'user_id', 'order_status_id', 'payment_id'];

    protected $appends = ['products'];

    protected $with = ['user', 'orderStatus', 'payment'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function products(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return collect((array) json_decode($value))->map(function ($row) {
                    $row = (array) $row;
                    $product = Product::select('uuid', 'price', 'title')->where('uuid', $row['product'])->first();
                    if ($product) {
                        return [
                            'uuid' => $product->uuid,
                            'product' => $product->title,
                            'price' => $product->price,
                            'quantity' => $row['quantity'],
                        ];
                    }
                })->toArray();
            }
        )->shouldCache();
    }
}
