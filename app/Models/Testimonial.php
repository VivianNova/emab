<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['client_name', 'message', 'rating', 'is_published'];

    protected $casts = [
        'rating' => 'integer',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

protected function casts(): array
{
    return ['rating' => 'integer', 'is_published' => 'boolean'];
}
}