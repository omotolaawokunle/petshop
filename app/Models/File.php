<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\Traits\HasUuids;

class File extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];
}
