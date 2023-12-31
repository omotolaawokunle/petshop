<?php

namespace App\Models;

use App\Services\Traits\HasUuids;
use App\Services\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory, HasUuids, Filterable;

    protected $guarded = [];

    protected $hidden = ['id'];
}
