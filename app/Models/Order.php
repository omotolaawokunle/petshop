<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\Traits\HasUuids;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = ['address' => 'array', 'products' => 'array', 'shipped_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
