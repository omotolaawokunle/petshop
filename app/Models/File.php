<?php

namespace App\Models;

use App\Services\Traits\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $hidden = ['id'];
}
