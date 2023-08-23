<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\Traits\HasUuids;
use App\Services\Traits\Filterable;

class Category extends Model
{
    use HasFactory, HasUuids, Filterable;

    protected $guarded;
}
