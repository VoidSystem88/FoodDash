<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
        'image_path',
        'price',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'float',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path && \Storage::disk('public')->exists($this->image_path)) {
            return \Storage::disk('public')->url($this->image_path);
        }

        return '';
    }
}