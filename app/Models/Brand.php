<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\Traits\HasUuids;
use App\Services\Traits\Filterable;

class Brand extends Model
{
    use HasFactory, HasUuids, Filterable;

    protected $guarded = [];
}
