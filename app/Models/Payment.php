<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\Traits\HasUuids;
use App\Enums\PaymentType;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $guarded;
    protected $casts = ['type' => PaymentType::class, 'details' => 'array'];
}
