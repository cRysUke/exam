<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('m-d-Y');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('m-d-Y');
    }

    protected $fillable =
    [
        'name',
        'category_id',
        'description',
    ];
}
