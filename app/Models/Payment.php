<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Services\Traits\HasUuids;
use App\Services\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, HasUuids, Filterable;

    protected $guarded;
    protected $casts = ['type' => PaymentType::class, 'details' => 'array'];
}
