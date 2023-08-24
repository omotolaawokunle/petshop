<?php

namespace App\Models;

use App\Services\Traits\HasUuids;
use App\Services\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasUuids, Filterable;

    protected $guarded = [];
    protected $casts = ['metadata' => 'array'];
    protected $hidden = ['id'];
    protected $appends = ['brand'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_uuid', 'uuid');
    }

    public function brand(): Attribute
    {
        return Attribute::make(get: fn () => Brand::where('uuid', $this->metadata['brand'])->first());
    }
}
